<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 6/6/2014
 * Time: 2:51 μμ
 */

namespace Product\Form;


use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;

class ProductFieldset extends BaseFieldset implements InputFilterProviderInterface
{

    public function init()
    {
        parent::__construct('product');

        $vocabulary = $this->getVocabulary();

        $this->add(array(
            "name" => "productNumber",
            "type" => "text"
        ));

        $this->add(array(
            "name" => "name",
            "type" => "text"
        ));

        $this->add(array(
            "name" => "description",
            "type" => "textarea"
        ));

//        $this->add(array(
//            'type' => 'text',
//            'name' => 'attributes',
//            'attributes' => array(
//                'class' => 'attributeSelect'
//            )
//        ));

        $this->add(array(
            "name" => "datasheet",
            "type" => "file"
        ));

        $this->add(array(
            "name" => "thumbnail",
            "type" => "file"
        ));

        $this->add(array(
            "name" => "specifications",
            "type" => "file"
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'relatedProducts',
            'attributes' => array(
                'multiple' => 'multiple',
            ),
            'options' => array(
                'object_manager' => $this->getEntityManager(),
                'target_class' => 'Product\Entity\Product',
                'property' => 'productNumber',
                'disable_inarray_validator' => true
            ),
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'productVariations',
            'attributes' => array(
                'multiple' => 'multiple',
            ),
            'options' => array(
                'object_manager' => $this->getEntityManager(),
                'target_class' => 'Product\Entity\Product',
                'property' => 'productNumber',
                'disable_inarray_validator' => true
            ),
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'category',
            'options' => array(
                'object_manager' => $this->getEntityManager(),
                'target_class' => 'Product\Entity\Category',
                "empty_option" => $this->getTranslator()->translate($vocabulary['EMPTY_OPTION']),
                'property' => 'name',
                'disable_inarray_validator' => true
            ),
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $vocabulary = $this->getVocabulary();
        return array(
            'productNumber' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_PRODUCT_NUMBER_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->getEntityManager()->getRepository('Product\Entity\Product'),
                            'fields' => 'productNumber',
                            'messages' => array(
                                'objectFound' => $this->getTranslator()->translate($vocabulary["ERROR_PRODUCT_NUMBER_EXISTS"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'name' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_NAME_EMPTY"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'description' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_DESCRIPTION_EMPTY"])
                            )
                        )
                    ),
                ),
//                'filters' => array(
//                    array('name' => 'StringTrim'),
//                    array(
//                        'name' => 'StripTags',
//                        'options' => array(
//                            'allowTags' => array('a','br','strong','del','em','ul','li','ol','img')
//                        )
//                    )
//                )
            ),
            'datasheet' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_PRODUCT_DATASHEET_EMPTY"])
                            )
                        )
                    ),
                ),
            ),
            'thumbnail' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_THUMBNAIL_EMPTY"])
                            )
                        )
                    ),
                ),
            ),
            'specifications' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_PRODUCT_SPECIFICATIONS_EMPTY"])
                            )
                        )
                    ),
                ),
            ),
            'relatedProducts' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'productVariations' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'category' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }

} 