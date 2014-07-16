<?php
namespace Application\Controller;

use Zend\Http\Request;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class SlideController
 * @package Application\Controller
 * @method string translate($string)
 * @method Request getRequest()
 */
class SlideController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";

    /**
     * The slide form
     *
     * @var \Zend\Form\Form
     */
    private $slideForm;

    /**
     * The slide repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $slideRepository;

    /**
     * The slide service
     *
     * @var \Application\Service\Slide
     */
    private $slideService;

    public function listAction(){
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            $slideRepository = $this->getSlideRepository();
            return new ViewModel(array(
                "slides" => $slideRepository->findAll(),
                "form" => $this->getSlideForm()
            ));
        }
        return $this->notFoundAction();
    }

    public function addAction(){
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getSlideService();
            if ($request->isPost()) {
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                $form = $this->getSlideForm();
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

    public function saveAction(){
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = $this->translate($this->vocabulary["MESSAGE_SLIDES_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getSlideService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_SLIDES_NOT_SAVED"]);
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
            $message = $this->translate($this->vocabulary["MESSAGE_SLIDE_REMOVED"]);
            if ($this->getSlideService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_SLIDE_NOT_REMOVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the slide form
     *
     * @return \Zend\Form\Form
     */
    public function getSlideForm(){
        if(null === $this->slideForm)
            $this->slideForm = $this->getServiceLocator()->get('slide_form');
        return $this->slideForm;
    }

    /**
     * Get the slide repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getSlideRepository(){
        if(null === $this->slideRepository)
            $this->slideRepository = $this->getEntityManager()->getRepository('Application\Entity\Slide');
        return $this->slideRepository;
    }

    /**
     * Get the slide service
     *
     * @return \Application\Service\Slide
     */
    public function getSlideService(){
        if(null === $this->slideService)
            $this->slideService = $this->getServiceLocator()->get('slide_service');
        return $this->slideService;
    }
}
