<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User_avatar
 *
 * @ORM\Table(name="user_avatar")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\User_avatarRepository")
 */
class User_avatar
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
     * @ORM\OneToOne(targetEntity="User_profile", mappedBy="user_avatar")
     */
    private $user_profile;

    /**
     * @var string
     *
     * @ORM\Column(name="link_avatar", type="text")
     */
    private $linkAvatar;


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
     * Set linkAvatar
     *
     * @param string $linkAvatar
     *
     * @return User_avatar
     */
    public function setLinkAvatar($linkAvatar)
    {
        $this->linkAvatar = $linkAvatar;

        return $this;
    }

    /**
     * Get linkAvatar
     *
     * @return string
     */
    public function getLinkAvatar()
    {
        return $this->linkAvatar;
    }

    /**
     * Set userProfile
     *
     * @param \AppBundle\Entity\User_profile $userProfile
     *
     * @return User_avatar
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
