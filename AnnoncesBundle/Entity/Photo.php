<?php

namespace AnnoncesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Photo
 *
 * @ORM\Table(name="photo")
 * @ORM\Entity(repositoryClass="AnnoncesBundle\Repository\PhotoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Photo
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
     * @ORM\Column(name="url", type="string", length=255, unique=false)
     */
    private $url;

    private $file;
    
    private $filename;
    
    /**
     * 
     * @var AnnnoncesBundle\Entity\Annonce
     * 
     * @ORM\ManyToOne(targetEntity="AnnoncesBundle\Entity\Annonce", inversedBy="photos")
     * @ORM\JoinColumn(unique=false)
     */
    private $annonce;
    
	/**
	 * 
	 * @var \DateTime
	 * 
	 * @ORM\Column(name="expiredAt", type="datetime", nullable=true)
	 */
    private $expiredAt;
    
    /**
     * @ORM\PrePersist()
     */
    public function preUpload()
    {
    	$this->setUrl($this->file->getClientOriginalName());
    }
    
    /**
     * @ORM\PostPersist()
     */
    public function postUpload()
    {
    	$this->file->move($this->getUploadRootDir(), $this->getId().'.'.mb_convert_encoding($this->getUrl(),'CP1252', 'auto'));
    }
    
    /**
     * 
     * @ORM\PreRemove()
     */
    public function preRemove()
    {
    	$this->filename = $this->getUploadRootDir().'/'.$this->getId().'.'.mb_convert_encoding($this->getUrl(),'CP1252', 'auto');
    }
    
    /**
     *
     * @ORM\PostRemove()
     */
    public function postRemove()
    {
    	if(file_exists($this->filename))
    	{
    		unlink($this->filename);
    	}
    }
   
    public function defineFileAfterLoad()
    {
    	$this->setFile(new File($this->getUploadRootDir().'/'.$this->getId().'.'.mb_convert_encoding($this->getUrl(),'CP1252', 'auto')));
    }
    
    public function getUploadRootDir()
    {
    	return __DIR__.'/../../../web'.$this->getUploadDir();
    }
    
    public function getUploadDir()
    {
    	return '/uploads/photos';
    }
    
    public function getExpiredAt()
    {
    	return $this->expiredAt;
    }
    
    public function setExpiredAt($expiredAt)
    {
    	$this->expiredAt = $expiredAt;
    }
    
    public function getFile()
    {
    	return $this->file;
    }
    
    public function setFile($file)
    {
    	$this->file = $file;
    }
    
    public function setAnnonce($annonce)
    {
    	//On enlève la date d'expiration pour éviter la suppression maintenant que la photo est utilisée.
    	$this->setExpiredAt(null);
    	$this->annonce = $annonce;
    }
    
    public function getAnnonce()
    {
    	return $this->annonce;
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
     * Set url
     *
     * @param string $url
     *
     * @return Photo
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

