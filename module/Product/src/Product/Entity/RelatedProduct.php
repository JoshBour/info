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
 * Class RelatedProduct
 * @package Product\Entity
 * @ORM\Entity
 * @ORM\Table(name="related_products")
 */
class RelatedProduct {
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="relatedProducts")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     **/
    private $product;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="relatedToProducts")
     * @ORM\JoinColumn(name="related_product_id", referencedColumnName="product_id")
     **/
    private $relatedProduct;

    /**
     * @ORM\Column(type="integer", length=3)
     */
    private $position;

    public function __construct($product, $relatedProduct, $position){
        $this->product = $product;
        $this->relatedProduct = $relatedProduct;
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
     * @param mixed $relatedProduct
     */
    public function setRelatedProduct($relatedProduct)
    {
        $this->relatedProduct = $relatedProduct;
    }

    /**
     * @return mixed
     */
    public function getRelatedProduct()
    {
        return $this->relatedProduct;
    }



} 