<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="username_idx", columns={"username"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class Users implements AdvancedUserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=23, nullable=false)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=88, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=255, nullable=false)
     */
    private $roles;


    /**
     * @ORM\ManyToMany(targetEntity="Groups", inversedBy="users")
     *
     */
    private $groups;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_at", type="integer", nullable=false)
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="confirmationToken", type="string", length=255, nullable=false)
     */
    private $confirmationtoken;


    public function __construct()
    {
        $this->groups = new ArrayCollection();
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
     * Set username
     *
     * @param string $username
     *
     * @return Users
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Users
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
     * Set salt
     *
     * @param string $salt
     *
     * @return Users
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param string $roles
     *
     * @return Users
     */
    public function setRoles(Array $roles)
    {
        $this->roles = implode(',', $roles);

        return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        //return  explode(',', $this->roles);

        $roles = array();
        foreach($this->getGroups() as $group){
            $roles[$group->getName()] = $group->getName();
        }

    //    foreach($this->getGroups() as $group){
    //        foreach($group->getRoles() as $role){
    //            $roles[$role] = $role;
    //        }
    //    }

        return array_values($roles);
    }

    /**
     * Test whether the user has the given role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }


    public function setGroups($groups)
    {
        $this->groups = $groups;

        return $this;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function addGroup(Groups $group) {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }

        return $this;
    }

    public function removeGroup(Groups $group){
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }
        return $this;
    }

    public function hasGroup(Groups $group){
        return $this->groups->contains($group);
    }


    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Users
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt->format('U');

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Users
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        // Make sure the image is never empty.
        if (empty($this->image)) {
            $this->image = 'placeholder.gif';
        }

        return $this->image;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Users
     */
    public function setIsActive($isActive)
    {
        $this->isActive = (bool) $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return (bool) $this->isActive;
    }

    /**
     * Set confirmationtoken
     *
     * @param string $confirmationtoken
     *
     * @return Users
     */
    public function setConfirmationtoken($confirmationtoken)
    {
        $this->confirmationtoken = $confirmationtoken;

        return $this;
    }

    /**
     * Get confirmationtoken
     *
     * @return string
     */
    public function getConfirmationtoken()
    {
        return $this->confirmationtoken;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is a no-op, since we never store the plain text credentials in this object.
     * It's required by UserInterface.
     *
     * @return void
     */
    public function eraseCredentials()
    {
    }

    /**
     * The Symfony Security component stores a serialized User object in the session.
     * We only need it to store the user ID, because the user provider's refreshUser() method is called on each request
     * and reloads the user by its ID.
     *
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
        ));
    }
    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            ) = unserialize($serialized);
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool    true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }
    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool    true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }
    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool    true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * Users are enabled by default.
     *
     * @return bool    true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->getIsActive();
    }

    /**
     * Set whether the user is enabled.
     *
     * @param bool $isEnabled
     */
    public function setEnabled($isEnabled)
    {
        return $this->setIsActive($isEnabled);
    }

    /**
     * Returns the name, if set, or else "Anonymous {id}".
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->getUsername() ?: 'Anonymous ' . $this->getId();
    }

//
//    /**
//     * @param int|null $timestamp
//     */
//    public function setTimePasswordResetRequested($timestamp)
//    {
//        $this->timePasswordResetRequested = $timestamp ?: null;
//    }
//
//    /**
//     * @return int|null
//     */
//    public function getTimePasswordResetRequested()
//    {
//        return $this->timePasswordResetRequested;
//    }
//
//    /**
//     * @param int $ttl Password reset request TTL, in seconds.
//     * @return bool
//     */
//    public function isPasswordResetRequestExpired($ttl)
//    {
//        $timeRequested = $this->getTimePasswordResetRequested();
//        if (!$timeRequested) {
//            return true;
//        }
//
//        return $timeRequested + $ttl < time();
//    }
}
