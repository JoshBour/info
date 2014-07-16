<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 7/6/2014
 * Time: 4:16 μμ
 */

namespace Product\Controller;


use Application\Controller\BaseController;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class CategoryController
 * @package Product\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class CategoryController extends BaseController
{

    const LAYOUT_ADMIN = "layout/admin";

    /**
     * The category form
     *
     * @var \Zend\Form\Form
     */
    private $categoryForm;

    /**
     * The category repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $categoryRepository;

    /**
     * The category service
     *
     * @var \Product\Service\Category
     */
    private $categoryService;

    public function indexAction()
    {
        $url = $this->params()->fromRoute("categoryUrl");
        if ($url) {
            /**
             * @var \Product\Entity\Category $category
             */
            $category = $this->getCategoryRepository()->findOneBy(array('url' => $url));
            if ($category) {
                $categories = array_merge(array($category), $category->getChildren()->toArray());

                if ($category) {
                    return new ViewModel(array(
                        "activeRoute" => "products_index",
                        "activeCategory" => $category,
                        "categories" => $categories,
                        "pageTitle" => "Info - " . $category->getName()
                    ));
                }
            }
        }
        return $this->notFoundAction();
    }

    public function listAction()
    {
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $this->getFileUtilService()->clearTempFiles();
            $categoryRepository = $this->getCategoryRepository();
            return new ViewModel(array(
                "categories" => $categoryRepository->findAll(),
                "form" => $this->getCategoryForm()
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
            $service = $this->getCategoryService();
            if ($request->isPost()) {
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                $form = $this->getCategoryForm();
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_CATEGORY_CREATED"]));
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
            $message = $this->translate($this->vocabulary["MESSAGE_CATEGORIES_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getCategoryService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_CATEGORIES_NOT_SAVED"]);
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
            $message = $this->translate($this->vocabulary["MESSAGE_CATEGORY_REMOVED"]);
            if ($this->getCategoryService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_CATEGORY_NOT_REMOVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the category form
     *
     * @return \Zend\Form\Form
     */
    public function getCategoryForm()
    {
        if (null === $this->categoryForm)
            $this->categoryForm = $this->getServiceLocator()->get('category_form');
        return $this->categoryForm;
    }

    /**
     * Get the category repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getCategoryRepository()
    {
        if (null === $this->categoryRepository)
            $this->categoryRepository = $this->getEntityManager()->getRepository('Product\Entity\Category');
        return $this->categoryRepository;
    }

    /**
     * Get the category service
     *
     * @return \Product\Service\Category
     */
    public function getCategoryService()
    {
        if (null === $this->categoryService)
            $this->categoryService = $this->getServiceLocator()->get('category_service');
        return $this->categoryService;
    }
} 