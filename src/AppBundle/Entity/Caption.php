<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Caption
 *
 * @ORM\Table(name="caption")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CaptionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Caption
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="id_instagram", type="string", length=255, unique=true, nullable=true)
     */
    private $idInstagram;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="created", type="string", length=255)
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="caption")
     */
    private $posts;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idInstagram
     *
     * @param string $idInstagram
     * @return Caption
     */
    public function setIdInstagram($idInstagram)
    {
        $this->idInstagram = $idInstagram;

        return $this;
    }

    /**
     * Get idInstagram
     *
     * @return string 
     */
    public function getIdInstagram()
    {
        return $this->idInstagram;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Caption
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set created
     *
     * @param string $created
     * @return Caption
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return string 
     */
    public function getCreated()
    {
        return $this->created;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add posts
     *
     * @param \AppBundle\Entity\Post $posts
     * @return Caption
     */
    public function addPost(\AppBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \AppBundle\Entity\Post $posts
     */
    public function removePost(\AppBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }



    /**
     * @ORM\PrePersist
     */
    public function setTitle($title)
    {
        $index = strpos($this->text, '#');
        $excerpt = substr($this->text, 0, $index);
        $this->title = $excerpt;

        $this->created = time();

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}
