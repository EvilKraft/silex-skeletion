<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Phones
 *
 * @ORM\Table(name="phones", uniqueConstraints={@ORM\UniqueConstraint(name="number_idx", columns={"number"})})
 * @ORM\Entity
 */
class Phones
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
     * @ORM\Column(name="number", type="string", length=25, nullable=false)
     */
    private $number;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;



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
     * Set number
     *
     * @param string $number
     *
     * @return Phones
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Phones
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
     * Get formatedNumber
     *
     * @return string
     */
    public function getFormatedNumber()
    {
        if(  preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $this->number,  $matches ) )
        {
            $result = '('.$matches[1].') '.$matches[2].'-'.$matches[3];
            return $result;
        }

        return $this->number;
    }
}
