<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User_profile
 *
 * @ORM\Table(name="user_profile")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\User_profileRepository")
 */
class User_profile
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
     * var int
     * One User_profile belonges to One User only
     * One-To-One, Bidirectional
     * @ORM\OneToOne(targetEntity="Users", inversedBy="user_profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $users;

    /**
     * var int
     * One User_profile belonges to One User_avatar only
     * One-To-One, Bidirectional
     * @ORM\OneToOne(targetEntity="User_avatar", inversedBy="user_profile")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     */
    private $user_avatar;

    /**
     * var int
     * One User_profile belonges to One User_phone only
     * One-To-One, Bidirectional
     * @ORM\OneToOne(targetEntity="User_phones", inversedBy="user_profile")
     * @ORM\JoinColumn(name="phone_id", referencedColumnName="id")
     */
     private $user_phone;

    /**
     * @var string
     *
     * @ORM\Column(name="bio", type="text")
     */
    private $bio;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="City", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="Country", type="string", length=255)
     */
    private $country;


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
     * Set bio
     *
     * @param string $bio
     *
     * @return User_profile
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get bio
     *
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User_profile
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return User_profile
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return User_profile
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set users
     *
     * @param \AppBundle\Entity\Users $users
     *
     * @return User_profile
     */
    public function setUsers(\AppBundle\Entity\Users $users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return \AppBundle\Entity\Users
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set userAvatar
     *
     * @param \AppBundle\Entity\User_avatar $userAvatar
     *
     * @return User_profile
     */
    public function setUserAvatar(\AppBundle\Entity\User_avatar $userAvatar = null)
    {
        $this->user_avatar = $userAvatar;

        return $this;
    }

    /**
     * Get userAvatar
     *
     * @return \AppBundle\Entity\User_avatar
     */
    public function getUserAvatar()
    {
        return $this->user_avatar;
    }

    /**
     * Set userPhone
     *
     * @param \AppBundle\Entity\User_phones $userPhone
     *
     * @return User_profile
     */
    public function setUserPhone(\AppBundle\Entity\User_phones $userPhone = null)
    {
        $this->user_phone = $userPhone;

        return $this;
    }

    /**
     * Get userPhone
     *
     * @return \AppBundle\Entity\User_phones
     */
    public function getUserPhone()
    {
        return $this->user_phone;
    }
}
