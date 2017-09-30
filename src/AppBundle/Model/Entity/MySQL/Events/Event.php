<?php

namespace AppBundle\Model\Entity\MySQL\Events;

/**
 * Event
 * @ORM\Entity
 * @ORM\Table(name="event")
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
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var int
     * @ORM\Column(type="integer")
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
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
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
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }
}

