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
use Zend\Validator\File\Size;
use Zend\Validator\Regex;

class CategoryFieldset extends BaseFieldset implements InputFilterProviderInterface
{

    public function init()
    {
        parent::__construct('category');

        $vocabulary = $this->getVocabulary();

        $this->add(array(
            "name" => "name",
            "type" => "text"
        ));

        $this->add(array(
            "name" => "description",
            "type" => "textarea"
        ));

        $this->add(array(
            "name" => "thumbnail",
            "type" => "file"
        ));

        $this->add(array(
            "name" => "position",
            "type" => "text"
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'parentCategory',
            'options' => array(
                'object_manager' => $this->getEntityManager(),
                'empty_option' => $this->getTranslator()->translate($vocabulary["EMPTY_OPTION"]),
                'target_class' => 'Product\Entity\Category',
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
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->getEntityManager()->getRepository('Product\Entity\Category'),
                            'fields' => 'name',
                            'messages' => array(
                                'objectFound' => $this->getTranslator()->translate($vocabulary["ERROR_CATEGORY_EXISTS"])
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
                'required' => false,
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
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array(
                        'name' => 'StripTags',
                        'options' => array(
                            'allowTags' => array('a','br','strong','del','em','ul','li','ol','img')
                        )
                    )
                )
            ),
            'thumbnail' => array(
                'required' => true,
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
            'position' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_POSITION_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => '/^[0-9]{1,3}$/',
                            'messages' => array(
                                Regex::NOT_MATCH => $this->getTranslator()->translate($vocabulary["ERROR_POSITION_INVALID"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'parentCategory' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }

} 