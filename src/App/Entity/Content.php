<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Content
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="content", indexes={@ORM\Index(name="parent_id", columns={"parent_id"}), @ORM\Index(name="lft", columns={"lft"}), @ORM\Index(name="scope", columns={"root_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ContentRepository")
 */
class Content
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
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer", nullable=false)
     */
    private $lft;

    /**
     * @var integer
     *
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer", nullable=false)
     */
    private $lvl;

    /**
     * @var integer
     *
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer", nullable=false)
     */
    private $rgt;


    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Content")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=30, nullable=true)
     */
    private $alias;

    /**
     * @ORM\OneToMany(targetEntity="ContentLangs", mappedBy="content", cascade={"persist", "remove"})
     */
    private $langs;

    public function __construct()
    {
        $this->langs = new ArrayCollection();
    }


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
     * Set lft
     *
     * @param string $lft
     *
     * @return Content
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param string $lvl
     *
     * @return Content
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param string $rgt
     *
     * @return Content
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }


     /**
     * Set alias
     *
     * @param string $alias
     *
     * @return Content
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setParent(Content $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setLangs($langs)
    {
        $this->langs = $langs;

        return $this;
    }

    public function getLangs()
    {
        return $this->langs;
    }

    public function addLang(ContentLangs $lang) {
        if (!$this->langs->contains($lang)) {
            $lang->setContent($this);
            $this->langs->add($lang);
        }

        return $this;
    }

    public function removeLang(ContentLangs $lang){
        if ($this->langs->contains($lang)) {
            $this->langs->removeElement($lang);
        }
        return $this;
    }

    public function hasLang($langId){
        $langId = (int) $langId;

        foreach($this->langs as $lang){
            if($lang->getLanguageId() == $langId){
                return true;
            }
        }
        return false;
    }

    public function getLaveledTitle(){
        //$title = $this->getAlias();
        //return '<span style="padding-left:'.($this->getLvl()*20).'px">'.$title.'</span>';

        $prefix = "";
        for ($i=2; $i<= $this->lvl; $i++){
            $prefix .= "&#160;&#160;&#160;&#160;&#160;";
        }
        return $prefix . ucwords($this->getAlias());
    }

    public function getChildren(){
        return $this->children;
    }
}
