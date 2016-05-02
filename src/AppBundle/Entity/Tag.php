<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections;

/**
 * Tag entity.
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagsRepository")
 *
 * @JMS\ExclusionPolicy("all")
 */
class Tag
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="name",
     *     unique=true,
     *     nullable=false,
     *     type="string",
     *     length=255
     * )
     *
     *@Assert\NotBlank(
     *     message="Tag name must be defined."
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("name")
     */
    private $name;

    /**
     * @var Collections\Collection
     *
     * @ORM\ManyToMany(
     *     targetEntity="AppBundle\Entity\Article",
     *     mappedBy="tags",
     *     cascade={"persist"}
     * )
     */
    private $articles;


    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->articles = new Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add article.
     *
     * @param Article $article
     *
     * @return $this
     *
     */
    public function addArticle(Article $article)
    {
        $this->articles->add($article);

        return $this;
    }

    /**
     * Get articles.
     *
     * @return Collections\ArrayCollection|Article[]
     */
    public function getArticles()
    {
        return $this->articles;
    }
}

