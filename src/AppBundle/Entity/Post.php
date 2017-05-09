<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \String
     *
     * @ORM\Column(name="id_instagram", type="string", unique=true, nullable=true)
     */
    private $id_instagram;

    /**
     * @var \String
     *
     * @ORM\Column(name="created", type="string")
     */
    private $created;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_instagram", type="boolean", nullable=true)
     */
    private $is_instagram;

    /**
     * @ORM\ManyToOne(targetEntity="Mime", inversedBy="posts")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="likes", type="string", length=255, nullable=true)
     */
    private $likes;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="posts", cascade={"persist"})
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="posts", cascade={"persist"})
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="Caption", inversedBy="posts", cascade={"persist", "remove"})
     */
    private $caption;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist"})
     */
    private $tags;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set id_instagram
     *
     * @param string $idInstagram
     * @return Post
     */
    public function setIdInstagram($idInstagram)
    {
        $this->id_instagram = $idInstagram;

        return $this;
    }

    /**
     * Get id_instagram
     *
     * @return string 
     */
    public function getIdInstagram()
    {
        return $this->id_instagram;
    }

    /**
     * Set created
     *
     * @param string $created
     * @return Post
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
     * Set is_instagram
     *
     * @param boolean $isInstagram
     * @return Post
     */
    public function setIsInstagram($isInstagram)
    {
        $this->is_instagram = $isInstagram;

        return $this;
    }

    /**
     * Get is_instagram
     *
     * @return boolean 
     */
    public function getIsInstagram()
    {
        return $this->is_instagram;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Post
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set likes
     *
     * @param string $likes
     * @return Post
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return string 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\Mime $type
     * @return Post
     */
    public function setType(\AppBundle\Entity\Mime $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\Mime 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     * @return Post
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set location
     *
     * @param \AppBundle\Entity\Location $location
     * @return Post
     */
    public function setLocation(\AppBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \AppBundle\Entity\Location 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set caption
     *
     * @param \AppBundle\Entity\Caption $caption
     * @return Post
     */
    public function setCaption(\AppBundle\Entity\Caption $caption = null)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get caption
     *
     * @return \AppBundle\Entity\Caption 
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Add tags
     *
     * @param \AppBundle\Entity\Tag $tags
     * @return Post
     */
    public function addTag(\AppBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \AppBundle\Entity\Tag $tags
     */
    public function removeTag(\AppBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(){
        $this->created = time();
    }
}
