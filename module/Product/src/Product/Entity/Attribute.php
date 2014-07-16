<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 27/6/2014
 * Time: 10:06 μμ
 */

namespace Product\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Attribute
 * @package Product\Entity
 * @ORM\Entity
 * @ORM\Table(name="attributes")
 */
class Attribute {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=11, name="attribute_id")
     */
    private $attributeId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="ProductAttribute", mappedBy="attribute")
     */
    private $products;

    public function __construct($name){
        $this->name = $name;
    }

    /**
     * @param mixed $attributeId
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
    }

    /**
     * @return mixed
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }


} 