<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 2:11 πμ
 */

namespace Post\Form;


use Zend\Form\Form;

class PostForm extends Form{
    /**
     * The post form constructor
     */
    public function __construct(){
        parent::__construct("postForm");

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
            'post' => array(
                'title',
                'content',
                'thumbnail',
            )
        ));
    }
} 