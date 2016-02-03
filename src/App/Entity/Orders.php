<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Orders
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
     * @ORM\Column(name="place_a", type="string", length=255, nullable=false)
     */
    private $placeA;

    /**
     * @var string
     *
     * @ORM\Column(name="place_b", type="string", length=255, nullable=false)
     */
    private $placeB;

    /**
     * @var integer
     *
     * @ORM\Column(name="come_at", type="integer", nullable=false)
     */
    private $comeAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="client_id", type="integer", nullable=false)
     */
    private $clientId;

    /**
     * @var integer
     *
     * @ORM\Column(name="driver_id", type="integer", nullable=false)
     */
    private $driverId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hours", type="boolean", nullable=false)
     */
    private $hours;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $price = '0.00';

    /**
     * @var integer
     *
     * @ORM\Column(name="created_at", type="integer", nullable=false)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="finished_at", type="integer", nullable=true)
     */
    private $finishedAt;


    /**
     * @ORM\ManyToOne(targetEntity="Clients", inversedBy="orders")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Drivers", inversedBy="orders")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id", nullable=FALSE)
     */
    protected $driver;


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
     * Set placeA
     *
     * @param string $placeA
     *
     * @return Orders
     */
    public function setPlaceA($placeA)
    {
        $this->placeA = $placeA;

        return $this;
    }

    /**
     * Get placeA
     *
     * @return string
     */
    public function getPlaceA()
    {
        return $this->placeA;
    }

    /**
     * Set placeB
     *
     * @param string $placeB
     *
     * @return Orders
     */
    public function setPlaceB($placeB)
    {
        $this->placeB = $placeB;

        return $this;
    }

    /**
     * Get placeB
     *
     * @return string
     */
    public function getPlaceB()
    {
        return $this->placeB;
    }

    /**
     * Set comeAt
     *
     * @param integer $comeAt
     *
     * @return Orders
     */
    public function setComeAt($comeAt)
    {
        $this->comeAt = $comeAt;

        return $this;
    }

    /**
     * Get comeAt
     *
     * @return integer
     */
    public function getComeAt()
    {
        return $this->comeAt;
    }

    /**
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return Orders
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get clientId
     *
     * @return integer
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set driverId
     *
     * @param integer $driverId
     *
     * @return Orders
     */
    public function setDriverId($driverId)
    {
        $this->driverId = $driverId;

        return $this;
    }

    /**
     * Get driverId
     *
     * @return integer
     */
    public function getDriverId()
    {
        return $this->driverId;
    }

    /**
     * Set hours
     *
     * @param boolean $hours
     *
     * @return Orders
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return boolean
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Orders
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Orders
     */
    public function setCreatedAt()
    {
        $this->createdAt = time();

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
     * Set finishedAt
     *
     * @param integer $finishedAt
     *
     * @return Orders
     */
    public function setFinishedAt(\DateTime $finishedAt)
    {
        $this->finishedAt = $finishedAt->format('U');

        return $this;
    }

    /**
     * Get finishedAt
     *
     * @return integer
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(Clients $client = null)
    {
        $this->client = $client;
        return $this;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function setDriver(Drivers $driver = null)
    {
        $this->driver = $driver;
        return $this;
    }

    public function getFormatedComeAt(){
        return date("d.m.Y H:i", $this->getComeAt());
    }

}
