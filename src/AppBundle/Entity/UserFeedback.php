<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserFeedback
 * @ORM\Entity
 * @ORM\Table(name="userfeedback")
 */
class UserFeedback
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
     * @ORM\Column(type="text")
     */
    private $text = "";

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $browserData = "";

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(type="datetime")
     */
    private $date = "";

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $url = "";

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    private $htmlContent = "";

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    private $picture;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $userUid = "";

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $userRoles = "";

    /**
     * @var string
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255)
     */
    private $userStamm = "";

    /**
     * @var string
     * @Assert\Ip
     * @ORM\Column(type="string", length=255)
     */
    private $userIp = "";

    /**
     * @return mixed
     */
    public function getUserUid()
    {
        return $this->userUid;
    }

    /**
     * @param mixed $userUid
     */
    public function setUserUid($userUid)
    {
        $this->userUid = $userUid;
    }

    /**
     * @return string
     */
    public function getUserRoles()
    {
        return $this->userRoles;
    }

    /**
     * @param string $userRoles
     */
    public function setUserRoles($userRoles)
    {
        $this->userRoles = $userRoles;
    }

    /**
     * @return string
     */
    public function getUserStamm()
    {
        return $this->userStamm;
    }

    /**
     * @param string $userStamm
     */
    public function setUserStamm($userStamm)
    {
        $this->userStamm = $userStamm;
    }

    /**
     * @return string
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * @param string $userIp
     */
    public function setUserIp($userIp)
    {
        $this->userIp = $userIp;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getBrowserData()
    {
        return $this->browserData;
    }

    /**
     * @param string $browserData
     */
    public function setBrowserData($browserData)
    {
        $this->browserData = $browserData;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getHtmlContent()
    {
        return $this->htmlContent;
    }

    /**
     * @param string $htmlContent
     */
    public function setHtmlContent($htmlContent)
    {
        $this->htmlContent = $htmlContent;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * binary coded png
     *
     * @param mixed $picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     *Returns
     *
     * @return string
     */
    public function getPictureAsBase64()
    {
        return substr(base64_encode(stream_get_contents($this->picture)), 19);
    }
}
