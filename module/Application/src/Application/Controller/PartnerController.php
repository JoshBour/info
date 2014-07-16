<?php
namespace Application\Controller;

use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;

/**
 * Class PartnerController
 * @package Application\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class PartnerController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";

    /**
     * The partner form
     *
     * @var \Zend\Form\Form
     */
    private $partnerForm;

    /**
     * The partner repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $partnerRepository;

    /**
     * The partner service
     *
     * @var \Application\Service\Partner
     */
    private $partnerService;

    public function listAction(){
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $partnerRepository = $this->getPartnerRepository();
            return new ViewModel(array(
                "partners" => $partnerRepository->findAll(),
                "form" => $this->getPartnerForm()
            ));
        }
        return $this->notFoundAction();
    }

    public function addAction(){
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getPartnerService();
            if ($request->isPost()) {
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                $form = $this->getPartnerForm();
                if ($service->create($data, $form)) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_PARTNER_CREATED"]));
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

    public function saveAction(){
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = $this->translate($this->vocabulary["MESSAGE_PARTNERS_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getPartnerService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_PARTNERS_NOT_SAVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    public function removeAction(){
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $id = $this->params()->fromPost("id");
            $success = 0;
            $message = $this->translate($this->vocabulary["MESSAGE_PARTNER_REMOVED"]);
            if ($this->getPartnerService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_PARTNER_NOT_REMOVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the partner form
     *
     * @return \Zend\Form\Form
     */
    public function getPartnerForm(){
        if(null === $this->partnerForm)
            $this->partnerForm = $this->getServiceLocator()->get('partner_form');
        return $this->partnerForm;
    }

    /**
     * Get the partner repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getPartnerRepository(){
        if(null === $this->partnerRepository)
            $this->partnerRepository = $this->getEntityManager()->getRepository('Application\Entity\Partner');
        return $this->partnerRepository;
    }

    /**
     * Get the partner service
     *
     * @return \Application\Service\Partner
     */
    public function getPartnerService(){
        if(null === $this->partnerService)
            $this->partnerService = $this->getServiceLocator()->get('partner_service');
        return $this->partnerService;
    }
}
