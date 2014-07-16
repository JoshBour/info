<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 2:13 πμ
 */

namespace Post\Factory;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Post\Entity\Post;
use Post\Form\PostFieldset;
use Post\Form\PostForm;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PostFormFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $entityManager
         */
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $formManager = $serviceLocator->get('FormElementManager');
        /**
         * @var PostFieldset $fieldset
         */
        $fieldset = $formManager->get('Post\Form\PostFieldset');
        $form = new PostForm();
        $hydrator = new DoctrineHydrator($entityManager, '\Post\Entity\Post');

        $fieldset->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Post);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);

        return $form;
    }
}