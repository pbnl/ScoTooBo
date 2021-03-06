<?php

namespace App\Entity;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Length(
     *     min=5,
     *     max=200,
     *     minMessage="This value is too short. It should have {{ limit }} characters or more",
     *     maxMessage="This value is too long. It should have {{ limit }} characters or less."
     * )
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $invitationLink;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $invitationDateFrom;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $invitationDateTo;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $participationFields;


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
     * Get string
     *
     * @return name
     */
    public function getName()
    {
        return $this->name;
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
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
        if ($this->price < 100) {
            return "0," . $this->price . "€";
        } else {
            $txt = strval($this->price);

            return substr_replace($txt, ',', -2, 0) . '€';
        }
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
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
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
     * Get dateFromToAsString
     *
     * @return \String
     */
    public function getDateFromToAsString()
    {
        return $this->dateFrom->format('Y-m-d H:i:s') . ' - ' . $this->dateTo->format('Y-m-d H:i:s');
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
     * Get invitationLink
     *
     * @return string
     */
    public function getInvitationLink()
    {
        return $this->invitationLink;
    }

    /**
     * Set invitationLink
     *
     * @param string $invitationLink
     *
     * @return Events
     */
    public function setInvitationLink($invitationLink)
    {
        $this->invitationLink = $invitationLink;

        return $this;
    }

    /**
     * Get invitationDateFrom
     *
     * @return \DateTime
     */
    public function getInvitationDateFrom()
    {
        return $this->invitationDateFrom;
    }

    /**
     * Set invitationDateFrom
     *
     * @param \DateTime $invitationDateFrom
     *
     * @return Events
     */
    public function setInvitationDateFrom($invitationDateFrom)
    {
        $this->invitationDateFrom = $invitationDateFrom;

        return $this;
    }

    /**
     * Get invitationDateTo
     *
     * @return \DateTime
     */
    public function getInvitationDateTo()
    {
        return $this->invitationDateTo;
    }

    /**
     * Set invitationDateTo
     *
     * @param \DateTime $invitationDateTo
     *
     * @return Events
     */
    public function setInvitationDateTo($invitationDateTo)
    {
        $this->invitationDateTo = $invitationDateTo;

        return $this;
    }

    /**
     * Get participationFields
     *
     * @return string
     */
    public function getParticipationFields()
    {
        return $this->participationFields;
    }

    /**
     * Set participationFields
     *
     * @param string $participationFields
     *
     * @return Events
     */
    public function setParticipationFields($participationFields)
    {
        $this->participationFields = $participationFields;

        return $this;
    }

    /**
     * Get participationFieldsAsArray
     *
     * @return array
     */
    public function getParticipationFieldsAsArray()
    {
        return json_decode($this->participationFields);
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
     * Set price
     *
     * @param integer $price
     *
     * @return Event
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }
}
