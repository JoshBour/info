<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 6/6/2014
 * Time: 3:27 μμ
 */

namespace Product\Factory;


use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Product\Entity\Product;
use Product\Form\ProductFieldset;
use Product\Form\ProductForm;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProductFormFactory implements FactoryInterface
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
         * @var ProductFieldset $fieldset
         */
        $fieldset = $formManager->get('Product\Form\ProductFieldset');
        $form = new ProductForm();
        $hydrator = new DoctrineHydrator($entityManager, '\Post\Entity\Post');

        $fieldset->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Product);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);

        return $form;
    }

} 