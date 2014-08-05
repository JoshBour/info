<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 5:55 μμ
 */

namespace Product\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Product
 * @package Product\Entity
 * @ORM\Entity(repositoryClass="Product\Repository\ProductRepository")
 * @ORM\Table(name="products")
 */
class Product {

    /**
     * @ORM\OneToMany(targetEntity="ProductAttribute", mappedBy="product")
     */
    private $attributes;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=11, name="product_id")
     */
    private $productId;

    /**
     * @ORM\Column(type="string", length=100, name="product_number")
     */
    private $productNumber;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $datasheet;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $thumbnail;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $specifications;

    /**
     * @ORM\OneToMany(targetEntity="RelatedProduct", mappedBy="product")
     */
    private $relatedProducts;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="RelatedProduct", mappedBy="relatedProduct")
     */
    private $relatedToProducts;

    /**
     * @ORM\OneToMany(targetEntity="ProductVariation", mappedBy="product")
     */
    private $productVariations;

    /**
     * @ORM\OneToMany(targetEntity="ProductVariation", mappedBy="variation")
     */
    private $variationToProducts;

    public function __construct(){
        $this->relatedProducts = new ArrayCollection();
        $this->relatedToProducts = new ArrayCollection();
        $this->productVariations = new ArrayCollection();
        $this->variationToProducts = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function addAttributes($attributes){
        if(is_array($attributes)){
            foreach($attributes as $attribute)
                $this->attributes->add($attribute);
        }else{
            $this->attributes->add($attributes);
        }
    }

    public function removeAttributes($attributes){
        if(is_array($attributes)){
            foreach($attributes as $attribute)
                $this->attributes->removeElement($attribute);
        }else{
            $this->attributes->removeElement($attributes);
        }
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $datasheet
     */
    public function setDatasheet($datasheet)
    {
        $this->datasheet = $datasheet;
    }

    /**
     * @return mixed
     */
    public function getDatasheet()
    {
        return $this->datasheet;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
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
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productNumber
     */
    public function setProductNumber($productNumber)
    {
        $this->productNumber = $productNumber;
    }

    /**
     * @return mixed
     */
    public function getProductNumber()
    {
        return $this->productNumber;
    }

    public function addProductVariations($productVariations){
        if(is_array($productVariations)){
            foreach($productVariations as $productVariation)
                $this->productVariations->add($productVariation);
        }else{
            $this->productVariations->add($productVariations);
        }
    }

    public function clearProductVariations(){
        $this->getProductVariations()->clear();
    }

    /**
     * @param mixed $productVariations
     */
    public function setProductVariations($productVariations)
    {
        $this->productVariations = $productVariations;
    }

    /**
     * @return mixed
     */
    public function getProductVariations()
    {
        return $this->productVariations;
    }

    public function getPrice(){
        foreach($this->attributes as $attribute){
            if($attribute->getAttribute()->getName() == "Price")
                return $attribute->getValue();
        }
        return false;
    }

    public function addRelatedProducts($relatedProducts){
        if(is_array($relatedProducts)){
            foreach($relatedProducts as $relatedProduct)
                $this->relatedProducts->add($relatedProduct);
        }else{
            $this->relatedProducts->add($relatedProducts);
        }
    }

    public function clearRelatedProducts(){
        $this->getRelatedProducts()->clear();
    }

    /**
     * @param mixed $relatedProducts
     */
    public function setRelatedProducts($relatedProducts)
    {
        $this->relatedProducts = $relatedProducts;
    }

    /**
     * @return ArrayCollection
     */
    public function getRelatedProducts()
    {
        return $this->relatedProducts;
    }

    /**
     * @param mixed $relatedToProducts
     */
    public function setRelatedToProducts($relatedToProducts)
    {
        $this->relatedToProducts = $relatedToProducts;
    }

    /**
     * @return mixed
     */
    public function getRelatedToProducts()
    {
        return $this->relatedToProducts;
    }

    /**
     * @param mixed $specifications
     */
    public function setSpecifications($specifications)
    {
        $this->specifications = $specifications;
    }

    /**
     * @param bool $useDefault
     * @return string
     */
    public function getSpecifications($useDefault = true)
    {
        return !$this->specifications && $useDefault ? "../default-light.png" : $this->specifications;
    }

    /**
     * @param mixed $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @param bool $useDefault
     * @return string
     */
    public function getThumbnail($useDefault = true)
    {

        return !$this->thumbnail && $useDefault ? "../default-light.png" : $this->thumbnail;
    }

    /**
     * @param mixed $variationToProducts
     */
    public function setVariationToProducts($variationToProducts)
    {
        $this->variationToProducts = $variationToProducts;
    }

    /**
     * @return mixed
     */
    public function getVariationToProducts()
    {
        return $this->variationToProducts;
    }


} 