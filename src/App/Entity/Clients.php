<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Clients
 *
 * @ORM\Table(name="clients", uniqueConstraints={@ORM\UniqueConstraint(name="mobile_idx", columns={"mobile"})}, indexes={@ORM\Index(name="name_idx", columns={"name"})})
 * @ORM\Entity
 */
class Clients
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
     * @ORM\Column(name="mobile", type="string", length=25, nullable=false)
     */
    private $mobile;

    /**
     * @var integer
     *
     * @ORM\Column(name="called_at", type="integer", nullable=true)
     */
    private $calledAt;


    /**
     * @ORM\OneToMany(targetEntity="Orders", mappedBy="client")
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
     * @return Clients
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
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Clients
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set calledAt
     *
     * @param integer $calledAt
     *
     * @return Clients
     */
    public function setCalledAt($calledAt)
    {
        $this->calledAt = $calledAt;

        return $this;
    }

    /**
     * Get calledAt
     *
     * @return integer
     */
    public function getCalledAt()
    {
        return $this->calledAt;
    }

    public function getOrders()
    {
        return $this->orders->toArray();
    }

    public function addOrder(Orders $order)
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setClient($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order)
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            $order->setClient(null);
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
        if(  preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $this->mobile,  $matches ) )
        {
            $result = '('.$matches[1].') '.$matches[2].'-'.$matches[3];
            return $result;
        }

        return $this->mobile;
    }
}
