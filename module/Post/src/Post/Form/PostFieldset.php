<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:56 πμ
 */

namespace Post\Form;


use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\Size;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class PostFieldset extends BaseFieldset implements InputFilterProviderInterface
{

    /**
     * The post fieldset initializer
     */
    public function init()
    {
        parent::__construct("post");

        $this->add(array(
            'name' => 'thumbnail',
            'type' => 'file',
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'text',

        ));

        $this->add(array(
            'name' => 'content',
            'type' => 'text',
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
            'title' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_POST_TITLE_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 50,
                            'messages' => array(
                                StringLength::INVALID => $this->getTranslator()->translate($vocabulary["ERROR_POST_TITLE_INVALID_LENGTH"])
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->getEntityManager()->getRepository('Post\Entity\Post'),
                            'fields' => 'title',
                            'messages' => array(
                                'objectFound' => $this->getTranslator()->translate($vocabulary["ERROR_POST_EXISTS"])
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => '/^\w{4,}/',
                            'messages' => array(
                                Regex::NOT_MATCH => $this->getTranslator()->translate($vocabulary["ERROR_POST_TITLE_INVALID"])
                            )
                        )
                    )
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'content' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_POST_CONTENT_EMPTY"])
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