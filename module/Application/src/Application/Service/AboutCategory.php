<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:48 πμ
 */

namespace Application\Service;


use Application\Entity\AboutCategory as AboutCategoryEntity;
use Doctrine\ORM\EntityRepository;

/**
 * Class Slide
 * @package Post\Service
 * @method EntityRepository getAboutCategoryRepository($namespace)
 */
class AboutCategory extends BaseService
{

    /**
     * Create a new slide
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form)
    {
        $aboutCategory = new AboutCategoryEntity();
        $em = $this->getEntityManager();

        $form->bind($aboutCategory);
        $form->setData($data);
        if (!$form->isValid()) return false;

        $aboutCategory->setContent(str_replace(array("<div>", "</div>"), array("<p>", "</p>"), $aboutCategory->getContent()));
        $aboutCategory->setUrl($this->getUrl($aboutCategory));
        try {
            $em->persist($aboutCategory);
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
        $aboutCategoryRepository = $this->getAboutCategoryRepository('application');
        foreach ($entities as $entity) {
            /**
             * @var AboutCategoryEntity $aboutCategory
             */
            $aboutCategory = $aboutCategoryRepository->find($entity["id"]);
            unset($entity["id"]);
            foreach ($entity as $key => $value) {
                if (!empty($value)) {
                    if ($key == "Content") {
                        $aboutCategory->setContent(str_replace(array("<div>", "</div>"), array("<p>", "</p>"), $value));
                    } else if ($key == "Title") {
                        if ($value != $aboutCategory->getTitle()) {
                            if ($aboutCategoryRepository->findBy(array("title" => $value))) {
                                return false;
                            } else {
                                $aboutCategory->setTitle($value);
                                $aboutCategory->setUrl($this->getUrl($aboutCategory));
                            }
                        }
                    } else {
                        $aboutCategory->{'set' . $key}($value);
                    }
                } else {
                    $aboutCategory->{'set' . $key}(null);
                }
            }
            $em->persist($aboutCategory);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Removes a about category from the database
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $em = $this->getEntityManager();
        $aboutCategory = $this->getAboutCategoryRepository('application')->find($id);
        if ($aboutCategory) {
            try {
                $em->remove($aboutCategory);
                $em->flush();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Get the about category url
     *
     * @param \Application\Entity\AboutCategory $aboutCategory
     * @return string
     */
    private function getUrl($aboutCategory)
    {
        $encodedUrl = $aboutCategory->encodeUrl();
        $posts = $this->getAboutCategoryRepository('application')->findBy(array("url" => $encodedUrl));
        return count($posts) > 0 ? $encodedUrl . '-' . (count($posts) + 1) : $encodedUrl;
    }
} 