<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:21 πμ
 */

namespace Post\Controller;


use Application\Controller\BaseController;
use Zend\Http\Request;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package Post\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class IndexController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";

    /**
     * The post form
     *
     * @var \Zend\Form\Form
     */
    private $postForm;

    /**
     * Get the post repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $postRepository;

    /**
     * Get the post service
     *
     * @var \Post\Service\Post
     */
    private $postService;

    public function indexAction()
    {
        $page = $this->params()->fromRoute("page", 1);
        $posts = new Paginator(new ArrayAdapter($this->getPostRepository()->findBy(array(), array("postDate" => "DESC"))));
        $posts->setCurrentPageNumber($page)
            ->setItemCountPerPage(6);
        return new ViewModel(array(
            "useBlackLayout" => true,
            "pageTitle" => "Info - Latest News",
            "bodyClass" => "postsPage",
            "posts" => $posts,
        ));
    }

    public function viewAction()
    {
        $postUrl = $this->params()->fromRoute("postUrl");
        if ($postUrl) {
            $post = $this->getPostRepository()->findOneBy(array("url" => $postUrl));
            if ($post) {
                return new ViewModel(array(
                    "useBlackLayout" => true,
                    "post" => $post,
                    "activeRoute" => "posts_index",
                    "pageTitle" => "Info - " . $post->getTitle()
                ));
            }
        }
        return $this->notFoundAction();
    }

    public function listAction()
    {
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $this->getFileUtilService()->clearTempFiles();
            $postRepository = $this->getPostRepository();
            return new ViewModel(array(
                "posts" => $postRepository->findAll(),
                "form" => $this->getPostForm()
            ));
        }
        return $this->notFoundAction();
    }

    public function addAction()
    {
        /**
         * @var \Zend\Http\Request $request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getPostService();
            if ($request->isPost()) {
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                $form = $this->getPostForm();
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_POST_CREATED"]));
                    return new JsonModel(array('redirect' => true));
                } else {
                    $viewModel = new ViewModel(array("form" => $form));
                    $viewModel->setTerminal(true);
                    return $viewModel;
                }
            }
        }
        return $this->notFoundAction();
    }

    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = $this->translate($this->vocabulary["MESSAGE_POSTS_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getPostService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_POSTS_NOT_SAVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    public function removeAction()
    {
        /**
         * @var \Zend\Http\Request $request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $id = $this->params()->fromPost("id");
            $success = 0;
            $message = $this->translate($this->vocabulary["MESSAGE_POST_REMOVED"]);
            if ($this->getPostService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_POST_NOT_REMOVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the post form
     *
     * @return \Zend\Form\Form
     */
    public function getPostForm()
    {
        if (null === $this->postForm)
            $this->postForm = $this->getServiceLocator()->get('post_form');
        return $this->postForm;
    }

    /**
     * Get the post repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getPostRepository()
    {
        if (null === $this->postRepository)
            $this->postRepository = $this->getEntityManager()->getRepository('Post\Entity\Post');
        return $this->postRepository;
    }

    /**
     * Get the post service
     *
     * @return \Post\Service\Post
     */
    public function getPostService()
    {
        if (null === $this->postService)
            $this->postService = $this->getServiceLocator()->get('post_service');
        return $this->postService;
    }
} 