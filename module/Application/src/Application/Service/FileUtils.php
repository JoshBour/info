<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 11/6/2014
 * Time: 10:11 μμ
 */

namespace Application\Service;


use Application\Entity\TempFile;
use Zend\Filter\File\Rename;

class FileUtils extends BaseService
{
    private $tempFileRepository;

    public function uploadFile($data)
    {
        // info on where to save the file
        $fileMeta = pathinfo($data['fileMeta']);
        $dirName = $fileMeta["dirname"];
        $fileName = $fileMeta["filename"];
        $fileInfo = pathinfo($data['uploadFile']['name']);
        $name = $fileName . '.' . $fileInfo['extension'];
        $loc = ROOT_PATH . '/' . $dirName . '/' . $fileName . '-temp.' . $fileInfo['extension'];
        $em = $this->getEntityManager();
        try {
            $filter = new Rename(array(
                'target' => $loc,
                'overwrite' => true
            ));
            $filter->filter($data['uploadFile']);

            $em->persist(new TempFile($name, $loc));
            $em->flush();

            chmod($loc, 0644);
            return $name;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function clearTempFiles()
    {
        $files = $this->getTempFileRepository()->findAll();
        $em = $this->getEntityManager();
        try {
            /**
             * @var \Application\Entity\TempFile $file
             */
            foreach ($files as $file) {
                self::deleteFile($file->getLocation());
                $em->remove($file);
            }
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getFilePath($file,$subdir,$type){
        $basePath = ROOT_PATH . '/' . $type . '/' . $subdir . '/' . $file;
        return file_exists($basePath) ? $basePath : false;

    }

    public static function deleteFile($file)
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function getTempFileRepository()
    {
        if (null == $this->tempFileRepository)
            $this->tempFileRepository = $this->getEntityManager()->getRepository('Application\Entity\TempFile');
        return $this->tempFileRepository;
    }
} 