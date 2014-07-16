<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 4/6/2014
 * Time: 1:27 πμ
 */

namespace Post\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class post
 * @package post\Entity
 * @ORM\Entity(repositoryClass="Post\Repository\PostRepository")
 * @ORM\Table(name="posts")
 */
class Post {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=11, name="post_id")
     */
    private $postId;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $thumbnail;

    /**
     * @ORM\OneToOne(targetEntity="User\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", name="post_date")
     */
    private $postDate;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $url;

    public function encodeUrl(){
        $words = array_slice(str_word_count($this->title,1),0,10);
        foreach($words as $key => $word){
            $words[$key] = strtolower($word);
        }
        return join('-',$words);
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $postDate
     */
    public function setPostDate($postDate)
    {
        if(!$postDate instanceof \DateTime)
            $postDate = new \DateTime($postDate);
        $this->postDate = $postDate;
    }

    /**
     * @return mixed
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * @param mixed $postId
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }


} 