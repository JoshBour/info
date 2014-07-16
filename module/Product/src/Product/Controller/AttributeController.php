<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 29/6/2014
 * Time: 6:40 μμ
 */

namespace Product\Controller;


use Application\Controller\BaseController;

class AttributeController extends BaseController{

    private $attributeService;

    public function addAction(){

    }

    public function updateAction(){

    }

    public function getAttributeService(){
        if(null === $this->attributeService)
            $this->getServiceLocator()->get('attribute_service');
        return $this->attributeService;
    }
} 