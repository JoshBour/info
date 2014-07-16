<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 6/6/2014
 * Time: 3:32 μμ
 */

namespace Product\Service;


use Application\Service\BaseService;
use Doctrine\ORM\EntityRepository;
use Product\Entity\Category as CategoryEntity;
use Zend\Filter\File\Rename;

/**
 * Class Category
 * @package Product\Service
 * @method EntityRepository getCategoryRepository($namespace)
 */
class Category extends BaseService
{

    /**
     * Create a new category
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form)
    {
        $category = new CategoryEntity();
        $em = $this->getEntityManager();

        $form->bind($category);
        $form->setData($data);
        if (!$form->isValid()) return false;
        if (!empty($data['category']['parentCategory'])) {
            if ($parentCategory = $this->getCategoryRepository("product")->find($data['category']['parentCategory']))
                $category->setParentCategory($parentCategory);
        } else {
            $category->setParentCategory(null);
        }
        if (!empty($data['category']['thumbnail'])) {
            switch ($data['category']['thumbnail']['type']) {
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
            $uniqueId = uniqid('category_');
            $loc = ROOT_PATH . '/images/categories/' . $uniqueId . '.' . $extension;
            $filter = new Rename(array(
                'target' => $loc,
                'overwrite' => true
            ));
            $filter->filter($data['category']['thumbnail']);
            chmod($loc, 0644);
            $category->setThumbnail($uniqueId . '.' . $extension);
        }
        $category->setUrl($category->encodeUrl());
        try {
            $em->persist($category);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function save($entities)
    {
        $em = $this->getEntityManager();
        $categoryRepository = $this->getCategoryRepository('product');
        foreach ($entities as $entity) {
            /**
             * @var \Product\Entity\Category $category
             */
            $category = $categoryRepository->find($entity["id"]);
            unset($entity["id"]);
            foreach ($entity as $key => $value) {
                if (!empty($value)) {
                    if ($key == "ParentCategory") {
                        $category->setParentCategory($categoryRepository->find($value));
                    } else if ($key == "Name") {
                        if ($category->getName() != $value) {
                            if ($categoryRepository->findBy(array('name' => $value)) != null) {
                                return false;
                            } else {
                                $category->setName($value);
                                $category->setUrl($category->encodeUrl());
                            }
                        }
                    } else if ($key == "Thumbnail") {
                        $thumbnail = $category->getThumbnail();
                        $loc = ROOT_PATH . '/images/categories/';
                        $splitName = explode('.', $entity["Thumbnail"]);
                        $templess = explode('-', $splitName[0]);
                        rename($loc . $entity["Thumbnail"], $loc . $templess[0] . '.' . $splitName[1]);
                        if (!empty($thumbnail)) unlink($loc . $thumbnail);
                        $category->setThumbnail($templess[0] . '.' . $splitName[1]);
                    } else {
                        $category->{'set' . $key}($value);
                    }
                } else {
                    $category->{'set' . $key}(null);
                }
            }
            $em->persist($category);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove a category from the database
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $em = $this->getEntityManager();
        $category = $this->getCategoryRepository('product')->find($id);
        if ($category) {
            try {
                $em->remove($category);
                $em->flush();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

} 