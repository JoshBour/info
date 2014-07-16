<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 6/6/2014
 * Time: 2:51 μμ
 */

namespace Product\Form;


use Zend\Form\Form;

class ProductForm extends Form{
    public function __construct(){
        parent::__construct("productForm");

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
            'product' => array(
                'productNumber',
                'name',
                'description',
                'datasheet',
                'thumbnail',
                'specifications',
                'category'
            )
        ));
    }
} 