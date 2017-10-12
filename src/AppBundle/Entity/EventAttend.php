<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EventsAttend;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EventAttend
 * @ORM\Entity(repositoryClass="Acme\StoreBundle\Entity\EventAttend")
 * @ORM\Table(name="eventAttend")
 */
class EventAttend
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
     * @var int
     * @ORM\Column(type="integer")
     */
    private $eventId;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $datetimeRegistration;

    /**
     * @Assert\Length(min=3)
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @Assert\Length(min=3)
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @Assert\Length(min=3)
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address_street;

    /**
     * @var string
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $address_nr;

    /**
     * @Assert\Length(min=5)
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $address_plz;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address_city;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stamm;

    /**
     * @var string
     * @ORM\Column(name="`group`", type="string", length=255, nullable=true)
     */
    private $group;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vegi;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function __construct()
    {
        $this->datetimeRegistration = new \DateTime();
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
     * Set eventId
     *
     * @param integer $eventId
     *
     * @return EventAttend
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return integer
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set datetimeRegistration
     *
     * @param \DateTime $datetimeRegistration
     *
     * @return EventAttend
     */
    public function setDatetimeRegistration($datetimeRegistration)
    {
        $this->datetimeRegistration = $datetimeRegistration;

        return $this;
    }

    /**
     * Get datetimeRegistration
     *
     * @return \DateTime
     */
    public function getDatetimeRegistration()
    {
        return $this->datetimeRegistration;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return EventAttend
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return EventAttend
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return EventAttend
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set addressStreet
     *
     * @param string $addressStreet
     *
     * @return EventAttend
     */
    public function setAddressStreet($addressStreet)
    {
        $this->address_street = $addressStreet;

        return $this;
    }

    /**
     * Get addressStreet
     *
     * @return string
     */
    public function getAddressStreet()
    {
        return $this->address_street;
    }

    /**
     * Set addressNr
     *
     * @param string $addressNr
     *
     * @return EventAttend
     */
    public function setAddressNr($addressNr)
    {
        $this->address_nr = $addressNr;

        return $this;
    }

    /**
     * Get addressNr
     *
     * @return string
     */
    public function getAddressNr()
    {
        return $this->address_nr;
    }

    /**
     * Set addressPlz
     *
     * @param integer $addressPlz
     *
     * @return EventAttend
     */
    public function setAddressPlz($addressPlz)
    {
        $this->address_plz = $addressPlz;

        return $this;
    }

    /**
     * Get addressPlz
     *
     * @return integer
     */
    public function getAddressPlz()
    {
        return $this->address_plz;
    }

    /**
     * Set addressCity
     *
     * @param string $addressCity
     *
     * @return EventAttend
     */
    public function setAddressCity($addressCity)
    {
        $this->address_city = $addressCity;

        return $this;
    }

    /**
     * Get addressCity
     *
     * @return string
     */
    public function getAddressCity()
    {
        return $this->address_city;
    }

    /**
     * Set stamm
     *
     * @param string $stamm
     *
     * @return EventAttend
     */
    public function setStamm($stamm)
    {
        $this->stamm = $stamm;

        return $this;
    }

    /**
     * Get stamm
     *
     * @return string
     */
    public function getStamm()
    {
        return $this->stamm;
    }

    /**
     * Set group
     *
     * @param string $group
     *
     * @return EventAttend
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set vegi
     *
     * @param boolean $vegi
     *
     * @return EventAttend
     */
    public function setVegi($vegi)
    {
        $this->vegi = $vegi;

        return $this;
    }

    /**
     * Get vegi
     *
     * @return boolean
     */
    public function getVegi()
    {
        return $this->vegi;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return EventAttend
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
