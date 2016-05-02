<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use AppBundle\Test\FunctionalTestCase;

/**
 * Test Article update cases.
 *
 * Class UpdateArticleTest
 */
class UpdateArticleTest extends FunctionalTestCase
{
    /**
     * Update existing article
     */
    public function testUpdateExistingArticle()
    {
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

        $articleId = $article->getId();

        $params = [
            'article' => [
                'title' => 'Test product title',
                'body'  => 'Product description',
                'tags'  => [
                    ['name' => 'tag 2'],
                ],
            ],
        ];

        $this->client->request('PUT', '/v1/articles/' . $articleId, $params);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());

        $actualData   = json_decode($response->getContent(), true);

        $expectedData = [
            'id'    => '' . $articleId . '',
            'title' => 'Test product title',
            'body'  => 'Product description',
            'tags'  => [
                1 => ['name' => 'tag 2'],
            ],
        ];

        // remove dynamic data
        $this->assertArrayHasKey('createdAt', $actualData);
        unset($actualData['createdAt']);

        $updatedArticle = $this->getEntityManager()->getRepository('AppBundle:Article')->findOneBy(
            ['id' => $articleId]
        );

        // Check if existing tag is re-used
        $this->assertTrue($updatedArticle->getTags()->contains($tag2));

        $this->assertEquals($expectedData, $actualData);
    }

    /**
     * When updating not existing article, error should be thrown.
     */
    public function testUpdateNotExistingArticle()
    {
        $this->client->request('GET', '/v1/articles/1234');
        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode(), $response->getContent());
        $this->assertContains('not found', $response->getContent());
    }
}
