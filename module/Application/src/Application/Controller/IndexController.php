<?php
namespace Application\Controller;

use Application\Model\SitemapXmlParser;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Http\Response\Stream;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package Application\Controller
 * @method Request getRequest()
 * @method Response getResponse()
 */
class IndexController extends BaseController
{
    private $contactForm;

    private $contentRepository;

    private $partnerRepository;

    private $postRepository;

    private $productRepository;

    private $slideRepository;

    public function homeAction()
    {
        $slides = $this->getSlideRepository()->findBy(array(), array("position" => "ASC"));
        $posts = $this->getPostRepository()->findBy(array(), array("postDate" => "DESC"), 3);
        return new ViewModel(array(
            "slides" => $slides,
            "posts" => $posts,
            "useBlackLayout" => true,
            "bodyClass" => "homePage"
        ));
    }

    public function contactAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        $form = $this->getContactForm();
        $vocabulary = $this->getVocabulary();
        $contentRepository = $this->getContentRepository();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $email = $contentRepository->findOneBy(array("target" => "contactEmail"))->getContent();
                $password = $contentRepository->findOneBy(array("target" => "contactPassword"))->getContent();
                $message = new Message();
                $body = "From: " . $data['contact']['sender'] . "

                {$data['contact']['body']}
                ";
                $message->addTo('info@infolightingco.com')
                    ->addFrom($data['contact']['sender'])
                    ->setSubject($data['contact']['subject'])
                    ->addReplyTo($data['contact']['sender'])
                    ->setBody($body)
                    ->setEncoding("UTF-8");

                $transport = new SmtpTransport();
                $options = new SmtpOptions(array(
                    'name' => 'infolightingco.com',
                    'host' => 'mail.infolightingco.com',
                    'port' => '25',
                    'connection_class' => 'login',
                    'connection_config' => array(
                       // 'ssl' => 'tls',
                        'username' => 'info@infolightingco.com', //info@infolightingco.com
                        'password' => '7jhuP%KP', //7jhuP%KP
                    )
                ));
                $transport->setOptions($options);
                $transport->send($message);

                $this->flashMessenger()->addMessage($vocabulary["EMAIL_SUCCESS"]);
                return $this->redirect()->toRoute('contact');
            }

        }
        return new ViewModel(array(
            "form" => $form,
            "content" => $contentRepository->findOneBy(array("target" => "contact")),
            "useBlackLayout" => true,
            "bodyClass" => "contactPage",
            "pageTitle" => "Info - Contact Us"
        ));
    }

    public function sitemapAction()
    {
        $this->getResponse()->getHeaders()->addHeaders(array('Content-type' => 'application/xml; charset=utf-8'));
        $type = $this->params()->fromRoute('type');
        $sitemapXmlParser = new SitemapXmlParser();
        $sitemapXmlParser->begin();
        if (!$type) {
            $sitemapXmlParser->addHeader("sitemapindex");
            $sitemapXmlParser->addSitemap("http://www.infolightingco.com/sitemap/static");
            $sitemapXmlParser->addSitemap("http://www.infolightingco.com/sitemap/dynamic/products");
            $sitemapXmlParser->addSitemap("http://www.infolightingco.com/sitemap/dynamic/posts");
        } else {
            if ($type == "static") {
                $pages = $this->getServiceLocator()->get('Config')['static_pages'];
                $sitemapXmlParser->addHeader("urlset");
                foreach ($pages as $page) {
                    $sitemapXmlParser->addUrl("http://www.infolightingco.com" . $page, "1.0");
                }
            } else {
                $index = $this->params()->fromRoute("index");

                $entities = $index == "products" ? $this->getProductRepository()->findAll() : $this->getPostRepository()->findAll();

                $sitemapXmlParser->addHeader("urlset", true);
                $i = 0;
                foreach ($entities as $entity) {
                    if ($i == 20) {
                        $sitemapXmlParser->show();
                        $i = 0;
                    }
                    $sitemapXmlParser->addEntityInfo($entity);
                    $i++;
                }
            }

        }
        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setTemplate('application/index/sitemap.xml');
        $sitemapXmlParser->close();
        $sitemapXmlParser->show();
        return $view;
    }

    public function downloadFileAction()
    {
        $fileName = urldecode($this->params()->fromRoute("fileName", null));
        echo ROOT_PATH . '/' . $fileName;
        var_dump(file_exists(ROOT_PATH . '/' . $fileName));
        if (!$fileName || !file_exists(ROOT_PATH . '/' . $fileName)) {
            return $this->notFoundAction();
        } else {
            $pathInfo = pathinfo($fileName);
            switch ($pathInfo['extension']) {
                case "pdf":
                    $type = "application/pdf";
                    break;
                case "xls":
                    $type = "application/vnd.ms-excel";
                    break;
                case "zip":
                    $type = "application/zip";
                    break;
                case "ppt":
                    $type = "application/vnd.ms-powerpoint";
                    break;
                default:
                    $type = "text/plain";
            }
            $response = new Stream();
            $response->setStream(fopen($fileName, 'r'));
            $response->setStatusCode(200);

            $headers = new \Zend\Http\Headers();
            $headers->addHeaderLine('Content-Type', $type)
                ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->addHeaderLine('Content-Length', filesize($fileName));

            $response->setHeaders($headers);
            return $response;
        }
    }

    public function uploadFileAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $name = $this->getFileUtilService()->uploadFile($data);
            $success = $name ? 1 : 0;
            return new JsonModel(array("success" => $success, "name" => $name));
        }
        return $this->notFoundAction();
    }

    /**
     * The change language action
     * Route: /change-language/:lang
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function changeLanguageAction()
    {
        $session = new Container("base");
        $response = $this->getResponse();
        $lang = $this->params()->fromRoute('lang');
        if ($lang == 'en') {
            $session->locale = 'en_US';
        } else if ($lang == 'el') {
            $session->locale = 'el_GR';
        } else {
            $session->locale = null;
        }
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        $this->redirect()->toUrl($url);
        return $response;
    }

    public function getContactForm()
    {
        if (null === $this->contactForm)
            $this->contactForm = $this->getServiceLocator()->get('contact_form');
        return $this->contactForm;
    }

    public function getContentRepository()
    {
        if (null == $this->contentRepository)
            $this->contentRepository = $this->getEntityManager()->getRepository('Application\Entity\Content');
        return $this->contentRepository;
    }

    public function getPartnerRepository()
    {
        if (null == $this->partnerRepository)
            $this->partnerRepository = $this->getEntityManager()->getRepository('Application\Entity\Partner');
        return $this->partnerRepository;
    }


    /**
     * @return \Post\Repository\PostRepository
     */
    public function getPostRepository()
    {
        if (null == $this->postRepository)
            $this->postRepository = $this->getEntityManager()->getRepository('Post\Entity\Post');
        return $this->postRepository;
    }

    /**
     * @return \Product\Repository\ProductRepository
     */
    public function getProductRepository()
    {
        if (null == $this->productRepository)
            $this->productRepository = $this->getEntityManager()->getRepository('Product\Entity\Product');
        return $this->productRepository;
    }

    public function getSlideRepository()
    {
        if (null == $this->slideRepository)
            $this->slideRepository = $this->getEntityManager()->getRepository('Application\Entity\Slide');
        return $this->slideRepository;
    }
}
