<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 2:13 πμ
 */

namespace Application\Factory;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Application\Entity\Partner;
use Application\Form\PartnerFieldset;
use Application\Form\PartnerForm;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PartnerFormFactory implements FactoryInterface
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
         * @var PartnerFieldset $fieldset
         */
        $fieldset = $formManager->get('Application\Form\PartnerFieldset');
        $form = new PartnerForm();
        $hydrator = new DoctrineHydrator($entityManager, 'Application\Entity\Partner');

        $fieldset->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Partner);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);

        return $form;
    }
}