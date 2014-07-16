<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace User\Form;


use Zend\Form\Form;

class UserForm extends Form
{
    /**
     * Add user form constructor
     */
    public function __construct()
    {
        parent::__construct('userForm');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'standardForm'
        ));

//        $this->add(array(
//            'name' => 'security',
//            'type' => 'Zend\Form\Element\Csrf'
//        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit'
        ));

        $this->setValidationGroup(array(
//            'security',
            'user' => array(
                'username',
                'password',
                'email',
            )
        ));
    }
} 