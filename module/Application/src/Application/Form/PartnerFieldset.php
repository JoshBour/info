<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:56 πμ
 */

namespace Application\Form;


use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\NotEmpty;

class PartnerFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    /**
     * The partner fieldset initializer
     */
    public function init()
    {
        parent::__construct("partner");

        $this->add(array(
            'name' => 'name',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'website',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'thumbnail',
            'type' => 'file',
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
                'required' => false,
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
            'website' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_WEBSITE_EMPTY"])
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
                    array('name' => 'StripTags')
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
            )
        );
    }


} 