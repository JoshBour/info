<?php
namespace Application\Form;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class ContactFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    public function init()
    {
        parent::__construct('contact');

        $vocabulary = $this->getVocabulary();

        $this->add(array(
            'name' => 'subject',
            'type' => 'text',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_SUBJECT"])
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_SUBJECT"])
            ),
        ));

        $this->add(array(
            'name' => 'body',
            'type' => 'text',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_BODY"]),
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_BODY"])
            ),
        ));

        $this->add(array(
            'name' => 'sender',
            'type' => 'email',
            'options' => array(
                'label' => $this->getTranslator()->translate($vocabulary["LABEL_SENDER"])
            ),
            'attributes' => array(
                'placeholder' => $this->getTranslator()->translate($vocabulary["PLACEHOLDER_SENDER"])
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        $vocabulary = $this->getVocabulary();
        return array(
            'subject' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_SUBJECT_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 10,
                            'max' => 30,
                            'messages' => array(
                                StringLength::INVALID => $this->getTranslator()->translate($vocabulary["ERROR_SUBJECT_INVALID_LENGTH"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'body' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_BODY_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'min' => 20,
                            'messages' => array(
                                StringLength::TOO_SHORT => $this->getTranslator()->translate($vocabulary["ERROR_BODY_INVALID_LENGTH"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'sender' => array(
                'required' => true,
                'validators' => array(
                    array (
                        'name' => 'Regex',
                        'options' => array(
                            'pattern'=>'/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/',
                            'messages' => array(
                                Regex::NOT_MATCH    => $this->getTranslator()->translate($vocabulary["ERROR_EMAIL_INVALID"]),
                            ),
                        ),
                        'break_chain_on_failure' => true
                    ),
                    array(
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                EmailAddress::INVALID_FORMAT => $this->getTranslator()->translate($vocabulary["ERROR_SENDER_INVALID"]),
                            )
                        )
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_SENDER_EMPTY"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
        );
    }

}