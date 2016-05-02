<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use JMS\Serializer\SerializerInterface;

/**
 * Tag service
 *
 * Class TagService
 */
class TagService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * TagService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface    $serializer
     */
    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer    = $serializer;
    }

    /**
     * Return tags that are used.
     *
     * @return Tag[]
     */
    public function listUsedTags()
    {
        $repository = $this->entityManager->getRepository('AppBundle:Tag');
        $tags       = $repository->listUsedTags();

        return $tags;
    }

    /**
     * Return tags that are not used.
     *
     * @return Tag[]
     */
    public function listUnusedTags()
    {
        $repository = $this->entityManager->getRepository('AppBundle:Tag');
        $tags       = $repository->listUnusedTags();

        return $tags;
    }

    /**
     * Get articles in array format
     *
     * @return array
     */
    public function getArticles()
    {
        $repository = $this->entityManager->getRepository('AppBundle:Article');
        $articles   = $repository->findAll();

        $serializedArticles = $this->serializer->serialize($articles, 'json');

        return json_decode($serializedArticles, true);
    }
}
