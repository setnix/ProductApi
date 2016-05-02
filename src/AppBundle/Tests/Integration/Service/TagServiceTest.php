<?php

namespace AppBundle\Tests\Integration\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use AppBundle\Service\TagService;
use AppBundle\Test\FunctionalTestCase;

/**
 * Test tag service cases.
 *
 * Class TagServiceTest
 */
class TagServiceTest extends FunctionalTestCase
{
    /**
     * @var TagService
     */
    protected $service;

    /**
     * Set up.
     */
    public function setUp()
    {
        parent::setUp();

        $this->service = $this->container->get('app.tags');
    }

    /**
     * Return tags that are used by articles
     */
    public function testReturnTagsThatAreUsedByArticles()
    {
        // Create not used tag
        $tag = new Tag();
        $tag->setName('not used tag');
        $this->getEntityManager()->persist($tag);
        $this->getEntityManager()->flush();

        // Create article and use tags
        $tag1 = new Tag();
        $tag1->setName('tag 1');
        $tag2 = new Tag();
        $tag2->setName('tag 2');
        $article = new Article();
        $article->setTitle('Product 1');
        $article->setBody('Body text 1');
        $article->addTag($tag1);
        $article->addTag($tag2);

        $this->populateDataBaseWithSampleData([$article]);

        $actualTags = [];
        /** @var Tag $tag */
        foreach ($this->service->listUsedTags() as $tag) {
            $actualTags[] = $tag->getName();
        };

        $expectedData = ['tag 1', 'tag 2'];

        $this->assertEquals($expectedData, $actualTags);
    }

    /**
     * Return tags that are not used by articles.
     */
    public function testReturnTagsThatAreNotUsedByArticles()
    {
        // Create not used tag
        $tag = new Tag();
        $tag->setName('not used tag');
        $this->getEntityManager()->persist($tag);
        $this->getEntityManager()->flush();

        // Create article and use tags
        $tag1 = new Tag();
        $tag1->setName('tag 1');
        $tag2 = new Tag();
        $tag2->setName('tag 2');
        $article = new Article();
        $article->setTitle('Product 1');
        $article->setBody('Body text 1');
        $article->addTag($tag1);
        $article->addTag($tag2);

        $this->populateDataBaseWithSampleData([$article]);

        $actualTags = [];
        /** @var Tag $tag */
        foreach ($this->service->listUnusedTags() as $tag) {
            $actualTags[] = $tag->getName();
        };

        $expectedData = ['not used tag'];

        $this->assertEquals($expectedData, $actualTags);
    }

    /**
     * Return all articles in array format.
     */
    public function testReturnAllArticlesInArrayFormat()
    {
        $tag = new Tag();
        $tag->setName('tag 1');
        $article = new Article();
        $article->setTitle('Product 1');
        $article->setBody('Body text 1');
        $article->addTag($tag);

        $this->populateDataBaseWithSampleData([$article]);

        $actualData = $this->service->getArticles();

        $expectedData = [
            'id'        => '' . $article->getId() . '',
            'title'     => 'Product 1',
            'body'      => 'Body text 1',
            'tags'      =>
                [
                    ['name' => 'tag 1'],
                ],
        ];

        $this->assertCount(1, $actualData);
        $this->assertArrayHasKey('createdAt', $actualData[0]);

        // remove dynamic data
        unset($actualData[0]['createdAt']);

        $this->assertEquals($expectedData, $actualData[0]);
    }
}
