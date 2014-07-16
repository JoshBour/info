<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:42 μμ
 */

namespace User\Service;


use Application\Service\BaseService;
use Doctrine\ORM\EntityRepository;
use User\Entity\User as UserEntity;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Class User
 * @package User\Service
 * @method EntityRepository getUserRepository($namespace)
 */
class User extends BaseService implements ServiceManagerAwareInterface
{
    /**
     * Create a new user
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form)
    {
        $user = new UserEntity();
        $em = $this->getEntityManager();

        $form->bind($user);
        $form->setData($data);
        if (!$form->isValid()) return false;
        $user->setPassword(UserEntity::getHashedPassword($user->getPassword()));
        $user->setCreateTime("now");
        try {
            $em->persist($user);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Update and save the user
     *
     * @param $entities
     * @return bool
     */
    public function save($entities)
    {
        $em = $this->getEntityManager();
        $userRepository = $this->getUserRepository('user');
        foreach ($entities as $entity) {
            /**
             * @var \User\Entity\User $user
             */
            $user = $userRepository->find($entity["id"]);
            unset($entity["id"]);
            foreach ($entity as $key => $value) {
                if (!empty($value)) {
                    if ($key == "Password" && $value != '**********') {
                        $user->setPassword(UserEntity::getHashedPassword($value));
                    } else {
                        $user->{'set' . $key}($value);
                    }
                } else {
                    $user->{'set' . $key}(null);
                }
            }
            $em->persist($user);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove an user from the database.
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $em = $this->getEntityManager();
        $user = $this->getUserRepository('user')->find($id);
        try {
            $em->remove($user);
            $em->flush();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}