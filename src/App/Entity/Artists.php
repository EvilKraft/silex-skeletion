<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Artists
 *
 * @ORM\Table(name="artists")
 * @ORM\Entity
 */
class Artists
{
    /**
     * @var integer
     *
     * @ORM\Column(name="artist_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $artistId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="biography", type="text", length=65535, nullable=true)
     */
    private $biography;

    /**
     * @var string
     *
     * @ORM\Column(name="soundcloud_url", type="string", length=255, nullable=true)
     */
    private $soundcloudUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="likes", type="integer", nullable=false)
     */
    private $likes = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="created_at", type="integer", nullable=false)
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="short_biography", type="text", length=65535, nullable=false)
     */
    private $shortBiography;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;



    /**
     * Get artistId
     *
     * @return integer
     */
    public function getArtistId()
    {
        return $this->artistId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Artists
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
     * Set biography
     *
     * @param string $biography
     *
     * @return Artists
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * Get biography
     *
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * Set soundcloudUrl
     *
     * @param string $soundcloudUrl
     *
     * @return Artists
     */
    public function setSoundcloudUrl($soundcloudUrl)
    {
        $this->soundcloudUrl = $soundcloudUrl;

        return $this;
    }

    /**
     * Get soundcloudUrl
     *
     * @return string
     */
    public function getSoundcloudUrl()
    {
        return $this->soundcloudUrl;
    }

    /**
     * Set likes
     *
     * @param integer $likes
     *
     * @return Artists
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set createdAt
     *
     * @param integer $createdAt
     *
     * @return Artists
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

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
     * Set shortBiography
     *
     * @param string $shortBiography
     *
     * @return Artists
     */
    public function setShortBiography($shortBiography)
    {
        $this->shortBiography = $shortBiography;

        return $this;
    }

    /**
     * Get shortBiography
     *
     * @return string
     */
    public function getShortBiography()
    {
        return $this->shortBiography;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Artists
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
        return $this->image;
    }
}
