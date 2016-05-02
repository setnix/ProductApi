<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article Entity.
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 *
 * @JMS\ExclusionPolicy("all")
 */
class Article
{
    /**
     * Identifier.
     *
     * @var integer
     *
     * @ORM\Column(
     *     name="id",
     *     type="integer"
     * )
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("id")
     */
    private $id;

    /**
     * Article title.
     *
     * @var string
     *
     * @ORM\Column(
     *     name="title",
     *     type="string",
     *     length=255
     * )
     *
     * @Assert\NotBlank(message="Article title must be defined.")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("title")
     */
    private $title;

    /**
     * Body text.
     *
     * @var string
     *
     * @ORM\Column(
     *     name="body",
     *     type="text",
     *     nullable=true
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("body")
     */
    private $body;

    /**
     * Date of creation.
     * Date format is UTC with time zone support for correct date-time output in different countries.
     *
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(
     *     name="createdAt",
     *     type="datetimetz"
     * )
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'Y-m-d\TH:i:s\Z', 'UTC'>")
     * @JMS\SerializedName("createdAt")
     */
    private $createdAt;

    /**
     * Tag collection.
     *
     * @var Collections\Collection
     *
     * @ORM\ManyToMany(
     *     targetEntity="AppBundle\Entity\Tag",
     *     inversedBy="articles",
     *     cascade={"persist"}
     * )
     * @ORM\JoinTable(name="articles_tags")
     *
     * @Assert\Count(
     *     min=1,
     *     minMessage="Article must have at least one tag."
     * )
     * @Assert\Valid()
     *
     * @JMS\Expose
     * @JMS\Type("array")
     * @JMS\SerializedName("tags")
     */
    protected $tags;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->tags = new Collections\ArrayCollection();
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
     * Set title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body.
     *
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Adds tag.
     *
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag)
    {
        $tag->addArticle($this);
        $this->tags->add($tag);

        return $this;
    }

    /**
     * Check if tag by given name exists.
     *
     * @param string $tagName
     *
     * @return boolean
     */
    public function hasTag($tagName)
    {
        foreach ($this->tags as $tag) {
            if ($tag->getName() === $tagName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes tag.
     *
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags.
     *
     * @return Collections\ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }
}

