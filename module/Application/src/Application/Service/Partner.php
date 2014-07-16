<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:48 πμ
 */

namespace Application\Service;


use Application\Entity\Partner as PartnerEntity;
use Doctrine\ORM\EntityRepository;
use Zend\Filter\File\Rename;

/**
 * Class Partner
 * @package Post\Service
 * @method EntityRepository getPartnerRepository($namespace)
 */
class Partner extends BaseService{

    /**
     * Create a new partner
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form){
        $partner = new PartnerEntity();
        $em = $this->getEntityManager();

        $form->bind($partner);
        $form->setData($data);
        if (!$form->isValid()) return false;
        if(!empty($data['partner']['thumbnail'])){
            switch ($data['partner']['thumbnail']['type']) {
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
            $uniqueId = uniqid('partner_');
            $loc = ROOT_PATH . '/images/partners/' . $uniqueId . '.' . $extension;
            $filter = new Rename(array(
                'target' => $loc,
                'overwrite' => true
            ));
            $filter->filter($data['partner']['thumbnail']);
            chmod($loc, 0644);
            $partner->setThumbnail($uniqueId . '.' . $extension);
        }
        try {
            $em->persist($partner);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function save($entities){
        $em = $this->getEntityManager();
        $partnerRepository = $this->getPartnerRepository('application');
        foreach ($entities as $entity) {
            /**
             * @var \Application\Entity\Partner $partner
             */
            $partner = $partnerRepository->find($entity["id"]);
            unset($entity["id"]);
            foreach ($entity as $key => $value) {
                if (!empty($value)) {
                    if ($key == "Thumbnail") {
                        $thumbnail = $partner->getThumbnail();
                        $loc = ROOT_PATH . '/images/partners/';
                        $splitName = explode('.', $entity["Thumbnail"]);
                        $templess = explode('-', $splitName[0]);
                        rename($loc . $entity["Thumbnail"], $loc . $templess[0] . '.' . $splitName[1]);
                        if (!empty($thumbnail)) unlink($loc . $thumbnail);
                        $partner->setThumbnail($templess[0] . '.' . $splitName[1]);
                    }else if($key == "Description"){
                        if(str_word_count($value) > 0)
                            $partner->setDescription(str_replace(array("<div>","</div>"),array("<p>","</p>"),$value));
                        else
                            $partner->setDescription(null);
                    } else {
                        $partner->{'set' . $key}($value);
                    }
                } else {
                    $partner->{'set' . $key}(null);
                }
            }
            $em->persist($partner);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Removes a post from the database
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $em = $this->getEntityManager();
        $partner = $this->getPartnerRepository('application')->find($id);
        if ($partner) {
            if ($thumbnail = $partner->getThumbnail())
                FileUtils::deleteFile(FileUtils::getFilePath($thumbnail, 'image', 'partners'));
            try {
                $em->remove($partner);
                $em->flush();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }
} 