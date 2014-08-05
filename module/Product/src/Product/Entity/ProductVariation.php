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
 * Class ProductVariation
 * @package Product\Entity
 * @ORM\Entity
 * @ORM\Table(name="products_variations")
 */
class ProductVariation {
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="productVariations")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     **/
    private $product;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="variationToProducts")
     * @ORM\JoinColumn(name="product_variation_id", referencedColumnName="product_id")
     **/
    private $variation;

    /**
     * @ORM\Column(type="integer", length=3)
     */
    private $position;

    public function __construct($product, $variation, $position){
        $this->product = $product;
        $this->variation = $variation;
        $this->position = $position;
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
     * @param mixed $variation
     */
    public function setVariation($variation)
    {
        $this->variation = $variation;
    }

    /**
     * @return mixed
     */
    public function getVariation()
    {
        return $this->variation;
    }



} 