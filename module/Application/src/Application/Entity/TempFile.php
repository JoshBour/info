<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 11/6/2014
 * Time: 11:26 μμ
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TempFile
 * @package Application\Entity
 * @ORM\Entity
 * @ORM\Table(name="temp_files")
 */
class TempFile {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $location;

    public function __construct($name,$location){
        $this->name = $name;
        $this->location = $location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


} 