<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 6/6/2014
 * Time: 3:32 μμ
 */

namespace Product\Service;


use Application\Model\StringUtils;
use Application\Service\BaseService;
use Application\Service\FileUtils;
use Doctrine\ORM\EntityRepository;
use Product\Entity\Attribute;
use Product\Entity\Product as ProductEntity;
use Product\Entity\ProductAttribute;
use Product\Entity\ProductVariation;
use Product\Entity\RelatedProduct;
use Zend\Filter\File\Rename;

/**
 * Class Product
 * @package Product\Service
 * @method EntityRepository getProductRepository($namespace)
 * @method EntityRepository getCategoryRepository($namespace)
 * @method EntityRepository getAttributeRepository($namespace)
 * @method EntityRepository getProductAttributeRepository($namespace)
 * @method EntityRepository getRelatedProductRepository($namespace)
 * @method EntityRepository getProductVariationRepository($namespace)
 */
class Product extends BaseService
{

    /**
     * Create a new product
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form)
    {
        $product = new ProductEntity();
        $productRepository = $this->getProductRepository("product");
        $attributeRepository = $this->getAttributeRepository('product');
        $em = $this->getEntityManager();
        if (!array_key_exists("extraData", $data)) {
            $form->bind($product);
            $form->setData($data);
            $form->isValid();
            return false;
        }
        $extraData = $data["extraData"];
        unset($data["extraData"]);
        $form->bind($product);
        $form->setData($data);
        if (!$form->isValid()) return false;
        if (!empty($data['product']['relatedProducts'])) {
            foreach ($data['product']['relatedProducts'] as $relatedProduct) {
                $product->addRelatedProducts($this->getProductRepository('product')->find($relatedProduct));
            }
        }
        if (!empty($data['product']['productVariations'])) {
            foreach ($data['product']['productVariations'] as $productVariation) {
                $product->addProductVariations($this->getProductRepository('product')->find($productVariation));
            }
        }
        if (!empty($data['product']['thumbnail'])) {
            switch ($data['product']['thumbnail']['type']) {
                case 'image/jpeg':
                    $extension = 'jpg';
                    break;
                case 'image/png':
                    $extension = 'png';
                    break;
                case 'image/gif':
                    $extension = 'gif';
                    break;
                default:
                    return false;
            }
            $uniqueId = uniqid('product_');
            $loc = ROOT_PATH . '/images/products/' . $uniqueId . '.' . $extension;
            $filter = new Rename(array(
                'target' => $loc,
                'overwrite' => true
            ));
            $filter->filter($data['product']['thumbnail']);
            chmod($loc, 0644);
            $product->setThumbnail($uniqueId . '.' . $extension);
        } else {
            $product->setThumbnail(null);
        }
        if (!empty($data['product']['specifications'])) {
            switch ($data['product']['specifications']['type']) {
                case 'image/jpeg':
                    $extension = 'jpg';
                    break;
                case 'image/png':
                    $extension = 'png';
                    break;
                case 'image/gif':
                    $extension = 'gif';
                    break;
                default:
                    return false;
            }
            $uniqueId = uniqid('specifications_');
            $loc = ROOT_PATH . '/images/products/' . $uniqueId . '.' . $extension;
            $filter = new Rename(array(
                'target' => $loc,
                'overwrite' => true
            ));
            $filter->filter($data['product']['specifications']);
            chmod($loc, 0644);
            $product->setSpecifications($uniqueId . '.' . $extension);
        } else {
            $product->setSpecifications(null);
        }

        if (!empty($data['product']['datasheet'])) {
            $uniqueId = uniqid('datasheet_');
            $loc = ROOT_PATH . '/data/datasheets/' . $uniqueId;
            $filter = new Rename(array(
                'target' => $loc,
                'overwrite' => true
            ));
            $filter->filter($data['product']['datasheet']);
            chmod($loc, 0644);
            $product->setDatasheet($uniqueId);
        }

        if (!empty($data['product']['category'])) {
            if ($category = $this->getCategoryRepository("product")->find($data['product']['category'])) {
                $product->setCategory($category);
            }
        } else {
            $product->setCategory(null);
        }
        $product->setDescription(str_replace(array("<div>", "</div>"), array("<p>", "</p>"), $product->getDescription()));
        $product->setSpecifications(str_replace(array("<div>", "</div>", "<div"), array("<p>", "</p>", "<p"), $product->getSpecifications()));
        try {
            $attributeList = array();
            $variationList = array();
            $relatedList = array();
            $em->persist($product);
            foreach ($extraData['attributes'] as $attributes) {
                $attribute = $attributeRepository->findOneBy(array("name" => $attributes["name"]));
                if (empty($attribute)) {
                    $attribute = new Attribute($attributes["name"]);
                    $em->persist($attribute);
                }
                $productAttribute = new ProductAttribute($product, $attribute, $attributes["value"], $attributes["position"]);
                $attributeList[] = $productAttribute;
            }
            if (isset($extraData['productVariations'])) {
                foreach ($extraData['productVariations'] as $variation) {
                    $variationProd = $productRepository->findOneBy(array("productNumber" => $variation["productNumber"]));
                    if ($variationProd) {
                        $join = new ProductVariation($product, $variation, $variation["position"]);
                    } else {
                        return false;
                    }
                    $variationList[] = $join;
                }
            }
            if (isset($extraData['relatedProducts'])) {
                foreach ($extraData['relatedProducts'] as $related) {
                    $relatedProd = $productRepository->findOneBy(array("productNumber" => $related["productNumber"]));
                    if ($relatedProd) {
                        $join = new RelatedProduct($product, $relatedProd, $related["position"]);
                    } else {
                        return false;
                    }
                    $relatedList[] = $join;
                }
            }
            $em->flush();
            foreach ($relatedList as $join) {
                $em->persist($join);
            }
            foreach ($variationList as $join) {
                $em->persist($join);
            }
            foreach ($attributeList as $attribute) {
                $em->persist($attribute);
            }
            $product->setAttributes($attributeList);
            if (isset($extraData['relatedProducts'])) $product->setRelatedProducts($relatedList);
            if (isset($extraData['productVariations'])) $product->setProductVariations($variationList);
            $em->persist($product);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function save($entities)
    {
        $em = $this->getEntityManager();
        $productRepository = $this->getProductRepository('product');
        $attributeRepository = $this->getAttributeRepository('product');
        $productAttributeRepository = $this->getProductAttributeRepository('product');
        $relatedProductRepository = $this->getRelatedProductRepository('product');
        $productVariationRepository = $this->getProductVariationRepository('product');
        foreach ($entities as $entity) {
            /**
             * @var \Product\Entity\Product $product
             */
            $product = $productRepository->find($entity["id"]);
            unset($entity["id"]);

            if (!isset($entity["RelatedProducts"])) {
                foreach ($product->getRelatedProducts() as $related) {
                    $em->remove($related);
                }
            }
            if (!isset($entity["ProductVariations"])) {
                foreach ($product->getProductVariations() as $variation) {
                    $em->remove($variation);
                }
            }
            foreach ($entity as $key => $value) {
                if (!empty($value)) {
                    if ($key == "RelatedProducts") {
                        $relatedList = array();
                        foreach ($value as $attributes) {
                            $related = $productRepository->findOneBy(array("productNumber" => $attributes["productNumber"]));
                            if (!empty($related)) {
                                $join = $relatedProductRepository->findOneBy(array("product" => $product, "relatedProduct" => $related));
                                if (empty($join)) {
                                    $join = new RelatedProduct($product, $related, $attributes["position"]);
                                } else {
                                    $join->setRelatedProduct($related);
                                    $join->setPosition($attributes["position"]);
                                }
                                $em->persist($join);
                                $relatedList[$attributes["productNumber"]] = $join;
                            } else {
                                return false;
                            }
                        }
                        foreach ($product->getRelatedProducts() as $related) {
                            if (!isset($relatedList[$related->getRelatedProduct()->getProductNumber()])) $em->remove($related);
                        }
                        $product->setRelatedProducts($relatedList);
                    } else if ($key == "ProductVariations") {
                        $variationList = array();
                        foreach ($value as $attributes) {
                            $variation = $productRepository->findOneBy(array("productNumber" => $attributes["productNumber"]));
                            if (!empty($variation)) {
                                $join = $productVariationRepository->findOneBy(array("product" => $product, "variation" => $variation));
                                if (empty($join)) {
                                    $join = new ProductVariation($product, $variation, $attributes["position"]);
                                } else {
                                    $join->setVariation($variation);
                                    $join->setPosition($attributes["position"]);
                                }
                                $em->persist($join);
                                $variationList[$attributes["productNumber"]] = $join;
                            } else {
                                return false;
                            }
                        }
                        foreach ($product->getProductVariations() as $variation) {
                            if (!isset($variationList[$variation->getVariation()->getProductNumber()])) $em->remove($variation);
                        }
                        $product->setProductVariations($variationList);
                    } else if ($key == "Attributes") {
                        $attributeList = array();
                        foreach ($value as $attributes) {
                            $attribute = $attributeRepository->findOneBy(array("name" => $attributes["name"]));
                            if (empty($attribute)) {
                                $attribute = new Attribute($attributes["name"]);
                                $em->persist($attribute);
                                $em->flush();
                                $productAttribute = new ProductAttribute($product, $attribute, $attributes["value"], $attributes["position"]);
                            } else {
                                $productAttribute = $productAttributeRepository->findOneBy(array("product" => $product, "attribute" => $attribute));
                                if (empty($productAttribute)) {
                                    $productAttribute = new ProductAttribute($product, $attribute, $attributes["value"], $attributes["position"]);
                                } else {
                                    $productAttribute->setValue($attributes["value"]);
                                    $productAttribute->setPosition($attributes["position"]);
                                }
                            }
                            $em->persist($productAttribute);
                            $attributeList[] = $productAttribute;
                        }
                        foreach ($product->getAttributes() as $prodAttr) {
                            if (!in_array($prodAttr, $attributeList)) $em->remove($prodAttr);
                        }
                        $product->setAttributes($attributeList);
                    } else if ($key == "Category") {
                        $product->setCategory($this->getCategoryRepository("product")->find($value));
                    } else if ($key == "Datasheet") {
                        $datasheet = $product->getDatasheet();
                        $loc = ROOT_PATH . '/data/datasheets/';
                        $splitName = explode('.', $value);
                        $templess = explode('-', $splitName[0]);
                        rename($loc . $templess[0] . '-temp.' . $splitName[1], $loc . $templess[0] . '.' . $splitName[1]);
                        if (!empty($datasheet)) unlink($loc . $datasheet);
                        $product->setDatasheet($templess[0] . '.' . $splitName[1]);
                    } else if ($key == "Thumbnail") {
                        $thumbnail = $product->getThumbnail(false);
                        $loc = ROOT_PATH . '/images/products/';
                        $splitName = explode('.', $value);
                        $templess = explode('-', $splitName[0]);
                        rename($loc . $value, $loc . $templess[0] . '.' . $splitName[1]);
                        if (!empty($thumbnail)) unlink($loc . $thumbnail);
                        $product->setThumbnail($templess[0] . '.' . $splitName[1]);
                    } else if ($key == "Specifications") {
                        $thumbnail = $product->getSpecifications(false);
                        $loc = ROOT_PATH . '/images/products/';
                        $splitName = explode('.', $value);
                        $templess = explode('-', $splitName[0]);
                        rename($loc . $value, $loc . $templess[0] . '.' . $splitName[1]);
                        if (!empty($thumbnail)) unlink($loc . $thumbnail);
                        $product->setSpecifications($templess[0] . '.' . $splitName[1]);
                    } else if ($key == "Description" || $key == "Specifications") {
                        $product->{'set' . $key}(str_replace(array("<div>", "</div>", "<div"), array("<p>", "</p>", "<p"), $value));
                    } else {
                        $product->{'set' . $key}($value);
                    }
                } else {
                    $product->{'set' . $key}(null);
                }
            }
            $em->persist($product);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Removes a product from the database
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $em = $this->getEntityManager();
        $product = $this->getProductRepository('product')->find($id);
        if ($product) {
            if ($thumbnail = $product->getThumbnail())
                FileUtils::deleteFile(FileUtils::getFilePath($thumbnail, 'image', 'products'));
            if ($datasheet = $product->getDatasheet())
                FileUtils::deleteFile(FileUtils::getFilePath($datasheet, 'data', 'datasheet'));
            try {
                $em->remove($product);
                $em->flush();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }


} 