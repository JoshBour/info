<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:48 πμ
 */

namespace Application\Service;


use Application\Entity\Slide as SlideEntity;
use Doctrine\ORM\EntityRepository;
use Zend\Filter\File\Rename;

/**
 * Class Slide
 * @package Post\Service
 * @method EntityRepository getSlideRepository($namespace)
 */
class Slide extends BaseService{

    /**
     * Create a new slide
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form){
        $slide = new SlideEntity();
        $em = $this->getEntityManager();

        $form->bind($slide);
        $form->setData($data);
        if (!$form->isValid()) return false;
        if(!empty($data['slide']['thumbnail'])){
            switch ($data['slide']['thumbnail']['type']) {
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
            $uniqueId = uniqid('slide_');
            $loc = ROOT_PATH . '/images/slides/' . $uniqueId . '.' . $extension;
            $filter = new Rename(array(
                'target' => $loc,
                'overwrite' => true
            ));
            $filter->filter($data['slide']['thumbnail']);
            chmod($loc, 0644);
            $slide->setThumbnail($uniqueId . '.' . $extension);
        }
        try {
            $em->persist($slide);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function save($entities){
        $em = $this->getEntityManager();
        $slideRepository = $this->getSlideRepository('application');
        foreach ($entities as $entity) {
            /**
             * @var \Application\Entity\Slide $slide
             */
            $slide = $slideRepository->find($entity["id"]);
            unset($entity["id"]);
            foreach ($entity as $key => $value) {
                if (!empty($value)) {
                    if ($key == "Post") {
                        $slide->setPost($this->getPostRepository("post")->find($value));
                    } else if ($key == "Thumbnail") {
                        $thumbnail = $slide->getThumbnail();
                        $loc = ROOT_PATH . '/images/slides/';
                        $splitName = explode('.', $entity["Thumbnail"]);
                        $templess = explode('-', $splitName[0]);
                        rename($loc . $entity["Thumbnail"], $loc . $templess[0] . '.' . $splitName[1]);
                        if (!empty($thumbnail)) unlink($loc . $thumbnail);
                        $slide->setThumbnail($templess[0] . '.' . $splitName[1]);
                    } else {
                        $slide->{'set' . $key}($value);
                    }
                } else {
                    $slide->{'set' . $key}(null);
                }
            }
            $em->persist($slide);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Removes a slide from the database
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $em = $this->getEntityManager();
        $slide = $this->getSlideRepository('application')->find($id);
        if ($slide) {
            try {
                $em->remove($slide);
                $em->flush();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }
} 