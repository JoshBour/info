<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 7:14 μμ
 */

namespace User\View\Helper;

use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class User extends AbstractHelper
{
    /**
     * The authentication service
     *
     * @var AuthenticationService
     */
    private $authService;

    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The service manager
     *
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Returns the current active user or false if none exists.
     *
     * @return \User\Entity\User|bool
     */
    public function __invoke()
    {
        return $this->getUser();
    }

    /**
     * Returns the active admin entity
     *
     * @return bool|\User\Entity\User
     */
    public function getUser()
    {
        $em = $this->getEntityManager();
        $auth = $this->getAuthService();
        if ($auth->hasIdentity()) {
            $user = $em->getRepository('User\Entity\User')->find($auth->getIdentity()->getAdminId());
        } else {
            $user = false;
        }
        return $user;
    }

    /**
     * Get the authentication service
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        if (null === $this->authService)
            $this->authService = $this->getServiceManager()->get('auth_service');
        return $this->authService;
    }

    /**
     * Get the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager)
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    /**
     * Get the service manager;
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}