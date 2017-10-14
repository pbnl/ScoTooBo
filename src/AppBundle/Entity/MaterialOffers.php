<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Event
 * @ORM\Entity
 * @ORM\Table(name="materialOffers")
 * @UniqueEntity("id")
 */
class MaterialOffers
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    ### @Assert\NotBlank()
    /**
     *
     * @var int
     * @ORM\Column(type="integer")
     */
    private $materialId;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @var int
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $shopName;



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
     * Set materialId
     *
     * @return MaterialOffers
     */
    public function setMaterialId($id)
    {
        $this->materialId = $id;

        return $this;
    }

    /**
     * Get materialId
     *
     * @return integer
     */
    public function getMaterialId()
    {
        return $this->materialId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MaterialOffers
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
     * Set shop url
     *
     * @param string $url
     *
     * @return MaterialOffers
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get shop url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return MaterialOffers
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get price in Euro
     *
     * @return string
     */
    public function getPriceInEuroWithEuroCharacter()
    {
        if ($this->price<100) {
            return "0,".$this->price."€";
        } else {
            $txt=strval($this->price);
            return substr_replace($txt, ',', -2, 0).'€';
        }
    }

    /**
     * Set shop name
     *
     * @param string $name
     *
     * @return MaterialOffers
     */
    public function setShopName($shopName)
    {
        $this->shopName = $shopName;

        return $this;
    }

    /**
     * Get shop name
     *
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }
}
