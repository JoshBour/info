<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:48 πμ
 */

namespace Application\Service;


use Doctrine\ORM\EntityRepository;

/**
 * Class Content
 * @package Post\Service
 * @method EntityRepository getContentRepository($namespace)
 */
class Content extends BaseService
{

    public function save($entities)
    {
        $em = $this->getEntityManager();
        $contentRepository = $this->getContentRepository('application');
        foreach ($entities as $entity) {
            /**
             * @var \Application\Entity\Content $content
             */
            $content = $contentRepository->find($entity["id"]);
            unset($entity["id"]);
            foreach ($entity as $key => $value) {
                if (!empty($value)) {
                    if ($key == "Content") {
                        $content->setContent(str_replace(array("<div>","</div>"),array("<p>","</p>"),$value));
                    } else {
                        $content->{'set' . $key}($value);
                    }
                } else {
                    $content->{'set' . $key}(null);
                }
            }
            $em->persist($content);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
} 