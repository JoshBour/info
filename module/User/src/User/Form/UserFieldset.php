<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace User\Form;


use Application\Form\BaseFieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class UserFieldset extends BaseFieldset implements InputFilterProviderInterface
{
    /**
     * Add user fieldset constructor
     */
    public function init()
    {
        parent::__construct('user');

        $this->add(array(
            'name' => 'username',
            'type' => 'text',
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'email',
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
            'username' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_USERNAME_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 15,
                            'messages' => array(
                                StringLength::INVALID => $this->getTranslator()->translate($vocabulary["ERROR_USERNAME_INVALID_LENGTH"])
                            )
                        )
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $this->getEntityManager()->getRepository('User\Entity\User'),
                            'fields' => 'username',
                            'messages' => array(
                                'objectFound' => $this->getTranslator()->translate($vocabulary["ERROR_USERNAME_EXISTS"])
                            )
                        )
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => '/^[a-zA-Z0-9_]{4,16}$/',
                            'messages' => array(
                                Regex::NOT_MATCH => $this->getTranslator()->translate($vocabulary["ERROR_USERNAME_INVALID_PATTERN"])
                            )
                        )
                    )
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'password' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => $this->getTranslator()->translate($vocabulary["ERROR_PASSWORD_EMPTY"])
                            )
                        )
                    ),
                    array(
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'min' => 4,
                            'max' => 20,
                            'messages' => array(
                                StringLength::INVALID => $this->getTranslator()->translate($vocabulary["ERROR_PASSWORD_INVALID_LENGTH"])
                            )
                        )
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags')
                )
            ),
            'email' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => array(
                            'messages' => array(
                                EmailAddress::INVALID_FORMAT => $this->getTranslator()->translate($vocabulary["ERROR_EMAIL_INVALID"]),
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