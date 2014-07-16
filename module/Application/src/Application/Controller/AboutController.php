<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 30/6/2014
 * Time: 12:04 πμ
 */

namespace Application\Controller;


use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;

/**
 * Class AboutController
 * @package Application\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class AboutController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";

    private $aboutCategoryForm;

    private $aboutCategoryRepository;

    private $aboutCategoryService;

    private $partnerRepository;

    public function indexAction()
    {
        $aboutCategories = $this->getAboutCategoryRepository()->findAll();
        $category = $this->params()->fromRoute("category");
        if($category == "partners"){
            $aboutCategory = $this->getPartnerRepository()->findAll();
        }else if($category == 'first'){
            $firstCategory = $this->getAboutCategoryRepository()->findBy(array(),array(),1);
            $aboutCategory = $firstCategory[0];
            $category = $aboutCategory->getUrl();
        }else{
            $aboutCategory = $this->getAboutCategoryRepository()->findOneBy(array("url" => $category));
        }
        return new ViewModel(array(
            "aboutCategories" => $aboutCategories,
            "aboutCategory" => $aboutCategory,
            "activeCategory" => $category,
            "bodyClass" => "aboutPage",
            "useBlackLayout" => true,
            "pageTitle" => "Info - About Us"
        ));
    }

    public function listAction(){
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            return new ViewModel(array(
                "aboutCategories" => $this->getAboutCategoryRepository()->findAll(),
                "form" => $this->getAboutCategoryForm()
            ));
        }
        return $this->notFoundAction();
    }

    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getAboutCategoryService();
            if ($request->isPost()) {
                $data = $request->getPost();
                $form = $this->getAboutCategoryForm();
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_SLIDE_CREATED"]));
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
            $message = $this->translate($this->vocabulary["MESSAGE_ABOUT_CATEGORIES_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getAboutCategoryService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_ABOUT_CATEGORIES_NOT_SAVED"]);
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
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $id = $this->params()->fromPost("id");
            $success = 0;
            $message = $this->translate($this->vocabulary["MESSAGE_ABOUT_CATEGORY_REMOVED"]);
            if ($this->getAboutCategoryService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_ABOUT_CATEGORY_NOT_REMOVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getAboutCategoryForm()
    {
        if (null === $this->aboutCategoryForm)
            $this->aboutCategoryForm = $this->getServiceLocator()->get('about_category_form');
        return $this->aboutCategoryForm;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAboutCategoryRepository()
    {
        if (null === $this->aboutCategoryRepository)
            $this->aboutCategoryRepository = $this->getEntityManager()->getRepository('Application\Entity\AboutCategory');
        return $this->aboutCategoryRepository;
    }

    /**
     * @return \Application\Service\AboutCategory
     */
    public function getAboutCategoryService()
    {
        if (null === $this->aboutCategoryService)
            $this->aboutCategoryService = $this->getServiceLocator()->get('about_category_service');
        return $this->aboutCategoryService;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getPartnerRepository()
    {
        if (null === $this->partnerRepository)
            $this->partnerRepository = $this->getEntityManager()->getRepository('Application\Entity\Partner');
        return $this->partnerRepository;
    }
} 