<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 30/6/2014
 * Time: 12:08 πμ
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AboutCategory
 * @package Application\Entity
 * @ORM\Entity
 * @ORM\Table(name="about_categories")
 */
class AboutCategory {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=5, name="about_category_id")
     */
    private $aboutCategoryId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

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
     * @param mixed $aboutCategoryId
     */
    public function setAboutCategoryId($aboutCategoryId)
    {
        $this->aboutCategoryId = $aboutCategoryId;
    }

    /**
     * @return mixed
     */
    public function getAboutCategoryId()
    {
        return $this->aboutCategoryId;
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


} 