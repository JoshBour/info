<?php
namespace User\Controller;


use Application\Controller\BaseController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;

/**
 * Class IndexController
 * @package User\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class IndexController extends BaseController
{
    const ROUTE_USER_LIST = "users";
    const CONTROLLER_NAME = 'User\Controller\Index';
    const LAYOUT_ADMIN = "layout/admin";

    /**
     * The add user form
     *
     * @var \Zend\Form\Form
     */
    private $userForm;

    /**
     * The user repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $userRepository;

    /**
     * The user repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $userService;

    /**
     * The user index action
     * Route: /users
     *
     * @return array|ViewModel
     */
    public function listAction()
    {
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $userRepository = $this->getUserRepository();
            return new ViewModel(array(
                "users" => $userRepository->findAll(),
                "form" => $this->getUserForm()
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * The user save action
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel
     */
    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = $this->translate($this->vocabulary["MESSAGE_USERS_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getUserService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_USERS_NOT_SAVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The user add action
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel|ViewModel
     */
    public function addAction()
    {
        /**
         * @var \Zend\Http\Request $request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getUserService();
            if ($request->isPost()) {
                $data = $request->getPost();
                $form = $this->getUserForm();
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_USER_CREATED"]));
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

    /**
     * The user remove action
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel
     */
    public function removeAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $id = $this->params()->fromPost("id");
            $success = 0;
            $message = $this->translate($this->vocabulary["MESSAGE_USER_REMOVED"]);
            if ($this->getUserService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_USER_NOT_REMOVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the add worker form
     *
     * @return \Zend\Form\Form
     */
    public function getUserForm()
    {
        if (null == $this->userForm)
            $this->userForm = $this->getServiceLocator()->get('user_form');
        return $this->userForm;
    }

    /**
     * Get the user repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getUserRepository()
    {
        if (null == $this->userRepository)
            $this->userRepository = $this->getEntityManager()->getRepository('User\Entity\User');
        return $this->userRepository;
    }

    /**
     * Gets the user service
     *
     * @return \User\Service\User
     */
    public function getUserService()
    {
        if (null == $this->userService)
            $this->userService = $this->getServiceLocator()->get('user_service');
        return $this->userService;
    }


} 