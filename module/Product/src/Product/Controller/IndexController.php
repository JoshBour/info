<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:21 πμ
 */

namespace Product\Controller;


use Application\Controller\BaseController;
use Application\Model\FileUtils;
use Doctrine\ORM\EntityRepository;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package Product\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class IndexController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";

    private $categoryRepository;

    private $productForm;

    private $productRepository;

    private $productService;

    public function indexAction()
    {
        $categories = $this->getCategoryRepository()->findBy(array("parentCategory" => null), array("position" => "ASC"));
        return new ViewModel(array(
            "categories" => $categories,
            "useBlackLayout" => true,
            "pageTitle" => "Info - Products"
        ));
    }

    public function viewAction()
    {
        $categoryUrl = $this->params()->fromRoute("categoryUrl",null);
        $productNumber = $this->params()->fromRoute("productNumber",null);
        if($productNumber){
            $category = $this->getCategoryRepository()->findOneBy(array("url"=>$categoryUrl));
            $product = $this->getProductRepository()->findOneBy(array("productNumber"=>$productNumber));
            return new ViewModel(array(
                "category" => $category,
                "product" => $product,
                "activeRoute" => "products_index",
                "bodyClass" => "productPage",
                "pageTitle" => "Info - " . $product->getName(),
                "tab" => $this->params()->fromRoute("tab")
            ));
        }
        return $this->notFoundAction();
    }

    public function searchAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $value = $this->params()->fromRoute("value");
            $viewModel->setVariable("products", $this->getProductRepository()->search($value));
            return $viewModel;
        }
        return $this->notFoundAction();
    }

    public function listAction()
    {
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $this->getFileUtilService()->clearTempFiles();
            $productRepository = $this->getProductRepository();
            return new ViewModel(array(
                "products" => $productRepository->findAll(),
                "form" => $this->getProductForm()
            ));
        }
        return $this->notFoundAction();
    }

    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getProductService();
            if ($request->isPost()) {
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                $form = $this->getProductForm();
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_PRODUCT_CREATED"]));
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
            $message = $this->translate($this->vocabulary["MESSAGE_PRODUCTS_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getProductService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_PRODUCTS_NOT_SAVED"]);
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
            $message = $this->translate($this->vocabulary["MESSAGE_PRODUCT_REMOVED"]);
            if ($this->getProductService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_PRODUCT_NOT_REMOVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the category repository
     *
     * @return EntityRepository
     */
    public function getCategoryRepository()
    {
        if (null == $this->categoryRepository)
            $this->categoryRepository = $this->getEntityManager()->getRepository('Product\Entity\Category');
        return $this->categoryRepository;
    }

    /**
     * Get the product form
     *
     * @return \Zend\Form\Form
     */
    public function getProductForm()
    {
        if (null === $this->productForm)
            $this->productForm = $this->getServiceLocator()->get('product_form');
        return $this->productForm;
    }

    /**
     * Get the product repository
     *
     * @return \Product\Repository\ProductRepository
     */
    public function getProductRepository()
    {
        if (null === $this->productRepository)
            $this->productRepository = $this->getEntityManager()->getRepository('Product\Entity\Product');
        return $this->productRepository;
    }

    /**
     * Get the product service
     *
     * @return \Product\Service\Product
     */
    public function getProductService()
    {
        if (null === $this->productService)
            $this->productService = $this->getServiceLocator()->get('product_service');
        return $this->productService;
    }
} 