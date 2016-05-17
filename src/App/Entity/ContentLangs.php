<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentLangs
 *
 * @ORM\Table(name="content_langs", indexes={@ORM\Index(name="id_lang_id_idx", columns={"content_id", "language_id"}), @ORM\Index(name="IDX_5340922984A0A3ED", columns={"content_id"})})
 * @ORM\Entity
 */
class ContentLangs
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
     * @var integer
     *
     * @ORM\Column(name="language_id", type="integer", nullable=false)
     */
    private $languageId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=255, nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="full", type="text", length=65535, nullable=true)
     */
    private $full;

    /**
     * @var \App\Entity\Content
     *
     * @ORM\ManyToOne(targetEntity="Content", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return ContentLangs
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set languageId
     *
     * @param integer $languageId
     *
     * @return ContentLangs
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * Get languageId
     *
     * @return integer
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ContentLangs
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
     * Set description
     *
     * @param string $description
     *
     * @return ContentLangs
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return ContentLangs
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set full
     *
     * @param string $full
     *
     * @return ContentLangs
     */
    public function setFull($full)
    {
        $this->full = $full;

        return $this;
    }

    /**
     * Get full
     *
     * @return string
     */
    public function getFull()
    {
        return $this->full;
    }

    /**
     * Set content
     *
     * @param \App\Entity\Content $content
     *
     * @return ContentLangs
     */
    public function setContent(\App\Entity\Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \App\Entity\Content
     */
    public function getContent()
    {
        return $this->content;
    }

    public function __toString(){
        return 'dddddddddddddd';
    }
}
