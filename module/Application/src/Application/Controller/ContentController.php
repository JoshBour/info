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
class ContentController extends BaseController
{
    const LAYOUT_ADMIN = "layout/admin";

    /**
     * The content repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $contentRepository;

    /**
     * The content service
     *
     * @var \Application\Service\Content
     */
    private $contentService;

    public function listAction(){
        if ($this->identity()) {
            $this->layout()->setTemplate(self::LAYOUT_ADMIN);
            return new ViewModel(array(
                "contents" => $this->getContentRepository()->findAll(),
            ));
        }
        return $this->notFoundAction();
    }

    public function saveAction(){
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = $this->translate($this->vocabulary["MESSAGE_CONTENTS_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getContentService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_CONTENTS_NOT_SAVED"]);
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
     * Get the content repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getContentRepository(){
        if(null === $this->contentRepository)
            $this->contentRepository = $this->getEntityManager()->getRepository('Application\Entity\Content');
        return $this->contentRepository;
    }

    /**
     * Get the content service
     *
     * @return \Application\Service\Content
     */
    public function getContentService(){
        if(null === $this->contentService)
            $this->contentService = $this->getServiceLocator()->get('content_service');
        return $this->contentService;
    }
}
