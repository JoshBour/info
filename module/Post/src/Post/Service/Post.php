<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:48 πμ
 */

namespace Post\Service;


use Application\Service\BaseService;
use Application\Service\FileUtils;
use Doctrine\ORM\EntityRepository;
use Post\Entity\Post as PostEntity;
use Zend\Filter\File\Rename;

/**
 * Class Post
 * @package Post\Service
 * @method EntityRepository getPostRepository()
 */
class Post extends BaseService{
    /**
     * The current user
     *
     * @var \User\Entity\User
     */
    private $user;

    /**
     * Create a new post
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form){
        $post = new PostEntity();
        $em = $this->getEntityManager();

        $form->bind($post);
        $form->setData($data);
        if (!$form->isValid()) return false;
        if(!empty($data['post']['thumbnail'])){
            switch ($data['post']['thumbnail']['type']) {
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
            $uniqueId = uniqid('post_');
            $loc = ROOT_PATH . '/images/posts/' . $uniqueId . '.' . $extension;
            $filter = new Rename(array(
                'target' => $loc,
                'overwrite' => true
            ));
            $filter->filter($data['post']['thumbnail']);
            chmod($loc, 0644);
            $post->setThumbnail($uniqueId . '.' . $extension);
        }
        $post->setUser($this->getUser());
        $post->setPostDate("now");
        $post->setUrl($this->getPostUrl($post));
        $post->setContent(str_replace(array("<div>", "</div>", "<div"), array("<p>", "</p>", "<p"), $post->getContent()));
        try {
            $em->persist($post);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function save($entities){
        $em = $this->getEntityManager();
        $postRepository = $this->getPostRepository('post');
        foreach ($entities as $entity) {
            /**
             * @var \Post\Entity\Post $post
             */
            $post = $postRepository->find($entity["id"]);
            unset($entity["id"]);
            foreach ($entity as $key => $value) {
                if (!empty($value)) {
                    if ($key == "Thumbnail") {
                        $thumbnail = $post->getThumbnail();
                        $loc = ROOT_PATH . '/images/posts/';
                        $splitName = explode('.', $entity["Thumbnail"]);
                        $templess = explode('-', $splitName[0]);
                        rename($loc . $entity["Thumbnail"], $loc . $templess[0] . '.' . $splitName[1]);
                        if (!empty($thumbnail)) unlink($loc . $thumbnail);
                        $post->setThumbnail($templess[0] . '.' . $splitName[1]);
                    }else if($key == "Title"){
                        if($value != $post->getTitle()){
                            if($postRepository->findBy(array("title"=>$value))){
                                return false;
                            }else{
                                $post->setTitle($value);
                                $post->setUrl($this->getPostUrl($post));
                            }
                    }else if ($key == "Content") {
                        $post->setContent(str_replace(array("<div>", "</div>", "<div"), array("<p>", "</p>", "<p"), $value));
                    }
                    } else {
                        $post->{'set' . $key}($value);
                    }
                } else {
                    $post->{'set' . $key}(null);
                }
            }
            $post->setUrl($this->getPostUrl($post));
            $em->persist($post);
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
        $post = $this->getPostRepository('post')->find($id);
        if ($post) {
            try {
                if ($thumbnail = $post->getThumbnail())
                    FileUtils::deleteFile(FileUtils::getFilePath($thumbnail, 'image', 'posts'));
                $em->remove($post);
                $em->flush();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Get the post url
     *
     * @param \Post\Entity\Post $post
     * @return string
     */
    private function getPostUrl($post){
        $encodedUrl = $post->encodeUrl();
        $posts = $this->getPostRepository('post')->findBy(array("url" => $encodedUrl));
        return count($posts) > 0 ? $encodedUrl.'-'.(count($posts)+1):$encodedUrl;
    }

    /**
     * Get the current user
     *
     * @return \User\Entity\User
     */
    public function getUser(){
        if(null === $this->user)
            $this->user = $this->getServiceManager()->get('ControllerPluginManager')->get('user')->getUser();
        return $this->user;
    }
} 