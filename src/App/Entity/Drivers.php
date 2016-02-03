<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Drivers
 *
 * @ORM\Table(name="drivers")
 * @ORM\Entity
 */
class Drivers
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=false)
     */
    private $surname;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="mobile_id", type="integer", nullable=true)
     */
    private $mobileId;

    /**
     * @ORM\OneToOne(targetEntity="Phones")
     * @ORM\JoinColumn(name="mobile_id", referencedColumnName="id")
     */
    private $mobile;


    /**
     * @ORM\OneToMany(targetEntity="Orders", mappedBy="driver")
     */
    protected $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Drivers
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * Set surname
     *
     * @param string $surname
     *
     * @return Drivers
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Drivers
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set mobileId
     *
     * @param integer $mobileId
     *
     * @return Drivers
     */
    public function setMobileId($mobileId)
    {
        $this->mobileId = $mobileId;

        return $this;
    }

    /**
     * Get mobileId
     *
     * @return integer
     */
    public function getMobileId()
    {
        return $this->mobileId;
    }

    /**
     * Get mobile
     *
     * @return integer
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    public function getOrders()
    {
        return $this->orders->toArray();
    }

    public function addOrder(Orders $order)
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setDriver($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order)
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            $order->setDriver(null);
        }

        return $this;
    }

    /**
     * Get formatedNumber
     *
     * @return string
     */
    public function getFormatedNumber()
    {
        return $this->getMobile()->getFormatedNumber();
    }
}
