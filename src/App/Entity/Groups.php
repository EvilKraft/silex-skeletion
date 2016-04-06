<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 */
class Groups
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Users", mappedBy="groups")
     */
    private $users;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=255, nullable=true)
     */
    private $roles;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Groups
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set name
     *
     * @param string $title
     *
     * @return Groups
     */
    public function setName($name)
    {
        $name = preg_replace('/\s+/', '_', trim($name));

        $this->name = strtoupper($name);

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return Groups
     */
    public function setRoles(Array $roles)
    {
        $this->roles = implode(',', $roles);

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return  array_filter(explode(',', $this->roles));
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

    /**
     * Add the given role to the user.
     *
     * @param string $role
     */
    public function addRole($role)
    {
        $role = strtoupper($role);

        if (!$this->hasRole($role)) {
            $roles = $this->getRoles();
            $roles[] = $role;
            $this->setRoles($roles);
        }

        return $this;
    }

    /**
     * Remove the given role from the user.
     *
     * @param string $role
     */
    public function removeRole($role)
    {
        $roles = $this->getRoles();

        if (false !== $key = array_search(strtoupper($role), $roles, true)) {
            unset($roles[$key]);
            $this->setRoles(array_values($roles));
        }
    }
}