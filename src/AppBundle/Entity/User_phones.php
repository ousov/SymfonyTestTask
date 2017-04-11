<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User_phones
 *
 * @ORM\Table(name="user_phones")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\User_phonesRepository")
 */
class User_phones
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
     * One User_avatar has only One User_profile.
     * One-To-One, Bidirectional
     * @ORM\OneToOne(targetEntity="User_profile", mappedBy="user_phone")
     */
    private $user_profile;
    /**
     * @var int
     *
     * @ORM\Column(name="phone_number", type="string", length=255)
     */
    private $phoneNumber;

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
     * Set phoneNumber
     *
     * @param integer $phoneNumber
     *
     * @return User_phones
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return int
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set userPhone
     *
     * @param \AppBundle\Entity\User_profile $userPhone
     *
     * @return User_phones
     */
    public function setUserPhone(\AppBundle\Entity\User_profile $userPhone = null)
    {
        $this->user_phone = $userPhone;
        return $this;
    }

    /**
     * Get userPhone
     *
     * @return \AppBundle\Entity\User_profile
     */
    public function getUserPhone()
    {
        return $this->user_phone;
    }

    /**
     * Set userProfile
     *
     * @param \AppBundle\Entity\User_profile $userProfile
     *
     * @return User_phones
     */
    public function setUserProfile(\AppBundle\Entity\User_profile $userProfile = null)
    {
        $this->user_profile = $userProfile;
        return $this;
    }

    /**
     * Get userProfile
     *
     * @return \AppBundle\Entity\User_profile
     */
    public function getUserProfile()
    {
        return $this->user_profile;
    }
}
