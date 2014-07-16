<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 30/6/2014
 * Time: 2:52 μμ
 */

namespace Product\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProductAttribute
 * @package Product\Entity
 * @ORM\Entity
 * @ORM\Table(name="products_attributes")
 */
class ProductAttribute {
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="attributes")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     **/
    private $product;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Attribute", inversedBy="products")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="attribute_id")
     **/
    private $attribute;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $value;

    /**
     * @ORM\Column(type="integer", length=3)
     */
    private $position;

    public function __construct($product, $attribute, $value, $position){
        $this->product = $product;
        $this->attribute = $attribute;
        $this->value = $value;
        $this->position = $position;
    }

    /**
     * @param mixed $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }


} 