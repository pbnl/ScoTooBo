<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Event
 * @ORM\Entity
 * @ORM\Table(name="event")
 * @UniqueEntity("name")
 */
class Event
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Assert\Range(
     *     min = 0,
     *     minMessage = "This value should be {{ limit }} or more."
     * )
     * @var int
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $price;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $dateFrom;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $dateTo;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @var string
     * @ORM\Column(type="text")
     */
    private $place;


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
     * Set string
     *
     * @param string $name
     *
     * @return Events
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get string
     *
     * @return name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Events
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Events
     */
    public function setPriceInCent($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price in Cent
     *
     * @return int
     */
    public function getPriceInCent()
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
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     *
     * @return Events
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     *
     * @return Events
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Events
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get dateFromToAsString
     *
     * @return \String
     */
    public function getDateFromToAsString()
    {
        return $this->dateFrom->format('Y-m-d H:i:s').' - '.$this->dateTo->format('Y-m-d H:i:s');
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }
}
