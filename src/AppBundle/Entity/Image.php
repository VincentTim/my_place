<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Services\FileResize as FileResize;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 */
class Image extends FileResize
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="width", type="string", length=255)
     */
    private $width;

    /**
     * @var string
     *
     * @ORM\Column(name="height", type="string", length=255)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\File()
     */
    private $file;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="Post", mappedBy="image", cascade={"persist", "remove"})
     */
    private $posts;

    //Upload
    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'upload/media/';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        //

        // use the standard_resolution file name here but you should
        // sanitize it at least to avoid any security issues

        $filename = sha1(uniqid(mt_rand(), true));
        $this->name = $filename.'.'.$this->getFile()->guessExtension();

        // move takes the target directory and then the
        // target filename to move to
        try {

            $this->getFile()->move(
                $this->getUploadRootDir(),
                $this->name
            );

            $original = $this->getUploadRootDir().$this->name;
            $target = $this->getUploadRootDir().'standard_resolution/'.$this->name;
            $size = getimagesize($original);

            $this->width = $size[0];//1000
            $this->height = $size[1];//600
            $this->type = $size['mime'];


            $infos = exif_read_data($original);

            //orientation 1 paysage et orientation 6 portrait
            switch($infos['Orientation']){
                case '1':
                    $destHeight = '640';
                    $destWidth = null;
                    break;
                case '6':
                    $destWidth = '640';
                    $destHeight = null;
                    break;
            }

            $this->resize($destWidth, $destHeight, $target, $original);
             return true;


        } catch(\Exception $e){
            var_dump($e->getMessage());
        }

        // clean up the file property as you won't need it anymore
        $this->file = null;
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
     * Set type
     *
     * @param string $type
     * @return Image
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set width
     *
     * @param string $width
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return string 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param string $height
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return string 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set post
     *
     * @param \AppBundle\Entity\Post $post
     * @return Image
     */
    public function setPost(\AppBundle\Entity\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \AppBundle\Entity\Post 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return File
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
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
     * @return Image
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
     * Set url
     *
     * @param string $url
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}
