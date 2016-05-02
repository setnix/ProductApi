<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;

/**
 * Tags repository
 *
 * Class TagsRepository
 */
class TagsRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Return tags that are used.
     *
     * @return Tag[]
     */
    public function listUsedTags()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->from('AppBundle:Tag', 'tag')
            ->select('tag')
            ->innerJoin('tag.articles', 'article')
            ->getQuery();

        $tags = $query->getResult();

        return $tags;
    }

    /**
     * Return tags that are not used.
     *
     * @return Tag[]
     */
    public function listUnusedTags()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->from('AppBundle:Tag', 'tag')
            ->select('tag')
            ->leftJoin('tag.articles', 'article')
            ->where('article.id is NULL')
            ->getQuery();

        $tags = $query->getResult();

        return $tags;
    }
}
