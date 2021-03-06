<?php

namespace AnnoncesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AnnoncesBundle\AnnoncesBundle;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use AnnoncesBundle\Constraints as AnnoncesBundleAssert;

/**
 * Annonce
 *
 * @ORM\Table(name="annonce")
 * @ORM\Entity(repositoryClass="AnnoncesBundle\Repository\AnnonceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Annonce
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", nullable=true)
     */
    private $prix;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expireAt", type="datetime")
     */
    private $expireAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePost", type="datetime")
     */
    private $datePost;

    /**
     * @var AnnoncesBundle\Entity\Category
     * 
     * @ORM\ManyToOne(targetEntity="AnnoncesBundle\Entity\Category")
     * @ORM\JoinColumn(unique=false)
     */
    private $category;
    
    /**
     * @var AnnoncesBundle\Entity\Ville
     * 
     * @AnnoncesBundleAssert\CityExist
     * @ORM\ManyToOne(targetEntity="AnnoncesBundle\Entity\Ville", cascade={"persist"})
     * @ORM\JoinColumn(unique=false)
     */
    private $city;
    
    /**
     * @var AnnoncesBundle\Entity\Photo
     * 
     * @ORM\OneToMany(targetEntity="AnnoncesBundle\Entity\Photo", cascade={"persist", "remove"}, mappedBy="annonce")
     */
    private $photos;
    
    public function __construct()
    {
    	$this->photos = new ArrayCollection();
    }
    
    public function hydrate(array $data)
    {
    	foreach($data as $propertyName => $value)
    	{
    		$method = 'set'.ucfirst($propertyName);
    		if(method_exists($this, $method))
    		{
    			$this->$method($value);
    		}
    	}
    }
    
    /**
     * @ORM\PrePersist()
     */
    public function updateDatePostAndExpireAt()
    {
    	$this->setDatePost(new \Datetime());
    	$date = new \Datetime();
    	$this->setExpireAt($date->add(new \DateInterval('P10D')));
    }
    
    /**
     *@Assert\Callback
     */
    public function isPhotosValid(ExecutionContextInterface $context)
    {
    	if(count($this->getPhotos()) > 3)
    	{
    		$context
    			->buildViolation('Vous ne pouvez inclure que 3 photos à cette annonce')
    			->atPath('photos')
    			->addViolation();
    	}
    	
    }
    
    public function getPhotos()
    {
    	return $this->photos;
    }
    
    public function setPhotos($photos)
    {
    	$this->photos = $photos;
    }
    
    public function addPhoto(Photo $photo)
    {
    	$photo->setAnnonce($this);
    	$this->photos[] = $photo;
    }
    
    public function removePhoto(Photo $photo)
    {
    	$photo->setAnnonce(null);
    	$this->photos->removeElement($photo);
    }
    
    /**
     * 
     * @return \AnnoncesBundle\AnnoncesBundle\Entity\Ville
     */
    public function getCity()
    {
    	return $this->city;
    }

    /**
     * 
     * @param AnnoncesBundle\Entity\Ville $city
     */
    public function setCity($city)
    {
    	$this->city = $city;
    }
    
    /**
     * 
     * @param AnnoncesBundle\Entity\Category $category
     */
    public function setCategory($category)
    {
    	$this->category = $category;
    }
    
    /**
     * 
     * @return \AnnoncesBundle\AnnoncesBundle\Entity\Category
     */
    public function getCategory()
    {
    	return $this->category;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Annonce
     */
    public function setTitle($title)
    {
        $this->title = $title;

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

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Annonce
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Annonce
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set expireAt
     *
     * @param \DateTime $expireAt
     *
     * @return Annonce
     */
    public function setExpireAt($expireAt)
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    /**
     * Get expireAt
     *
     * @return \DateTime
     */
    public function getExpireAt()
    {
        return $this->expireAt;
    }

    /**
     * Set datePost
     *
     * @param \DateTime $datePost
     *
     * @return Annonce
     */
    public function setDatePost($datePost)
    {
        $this->datePost = $datePost;

        return $this;
    }

    /**
     * Get datePost
     *
     * @return \DateTime
     */
    public function getDatePost()
    {
        return $this->datePost;
    }
}

