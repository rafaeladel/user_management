<?php

namespace Tsk\UserBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Tsk\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 * @Assert\GroupSequence({"FormCreate", "FormEdit", "User"})
 */
class User implements AdvancedUserInterface, \Serializable
{
    private $_listeners = array();
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank(groups={"FormEdit", "FormCreate"})
     */
    protected $username;

    /**
     * @Assert\NotBlank(groups={"FormCreate"})
     * @Assert\Length(min="4", minMessage="Password must be 4 characters at least", groups={"FormCreate"})
     */
    protected $plain_password;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\Email(groups={"FormEdit", "FormCreate"});
     */
    protected $email;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_active;

    /**
     * @ORM\Column(type="string", length=59)
     * @Assert\NotBlank(groups={"FormEdit", "FormCreate"})
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=59)
     * @Assert\NotBlank(groups={"FormEdit", "FormCreate"})
     */
    protected $lastname;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users", cascade={"persist"})
     * @Assert\NotBlank
     */
    protected $roles;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $salt;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->salt = md5(uniqid(null, true));
    }

    /**
     * @ORM\PrePersist
     */
    public function setIsActiveValue()
    {
        $this->setIsActive(true);
    }

    public function getPlainPassword()
    {
        return $this->plain_password;
    }

    public function setPlainPassword($plain_password)
    {
        $this->plain_password = $plain_password;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->getIsActive();
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

    public function serialize()
    {
        return serialize(array($this->getUsername()));
    }


    public function unserialize($serialized)
    {
        list($this->username) = unserialize($serialized);
    }


    public function getId()
    {
        return $this->id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;

        return $this;
    }

    public function getIsActive()
    {
        return $this->is_active;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function addRole(Role $roles)
    {
        $this->roles[] = $roles;
    
        return $this;
    }

    public function removeRole(Role $roles)
    {
        $this->roles->removeElement($roles);
    }


}