<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use AppBundle\Test\FunctionalTestCase;

/**
 * Test Article read cases.
 *
 * Class ReadArticleTest
 */
class ReadArticleTest extends FunctionalTestCase
{
    /**
     * Create sample data.
     */
    protected function createSampleData()
    {
        $tag1 = new Tag();
        $tag1->setName('tag 1');
        $tag2 = new Tag();
        $tag2->setName('tag 2');

        $article1 = new Article();
        $article1->setTitle('Product 1');
        $article1->setBody('Body text 1');
        $article1->addTag($tag1);
        $article1->addTag($tag2);

        $article2 = new Article();
        $article2->setTitle('Product 2');
        $article2->setBody('Body text 2');
        $article2->addTag($tag1);

        return [$article1, $article2];
    }

    /**
     * When requesting without id, all articles should be returned.
     */
    public function testReadAllProducts()
    {
        $this->populateDataBaseWithSampleData($this->createSampleData());

        $this->client->request('GET', '/v1/articles');
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());

        $actualData   = json_decode($response->getContent(), true);
        $expectedData = [
            [
                'title' => 'Product 1',
                'body'  => 'Body text 1',
                'tags'  => [
                    ['name' => 'tag 1'],
                    ['name' => 'tag 2'],
                ],
            ],
            [
                'title' => 'Product 2',
                'body'  => 'Body text 2',
                'tags'  => [
                    ['name' => 'tag 1'],
                ],
            ],
        ];

        foreach (array_keys($actualData) as $index) {
            $this->assertArrayHasKey('id', $actualData[$index]);
            $this->assertArrayHasKey('createdAt', $actualData[$index]);

            // remove dynamic data
            unset($actualData[$index]['id']);
            unset($actualData[$index]['createdAt']);
        }

        $this->assertEquals($expectedData, $actualData);
    }

    /**
     * When article id is provided, article data should be returned.
     */
    public function testReadSingleArticle()
    {
        $tag1 = new Tag();
        $tag1->setName('tag 1');

        $article1 = new Article();
        $article1->setTitle('Product 1');
        $article1->setBody('Body text 1');
        $article1->addTag($tag1);

        $this->populateDataBaseWithSampleData([$article1]);

        $articleId = $article1->getId();

        $this->client->request('GET', '/v1/articles/' . $articleId);
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());

        $actualData   = json_decode($response->getContent(), true);
        $expectedData = [
            'id'        => '' . $articleId .'',
            'title'     => 'Product 1',
            'body'      => 'Body text 1',
            'tags'      => [
                ['name' => 'tag 1'],
            ]
        ];

        // remove dynamic data
        $this->assertArrayHasKey('createdAt', $actualData);
        unset($actualData['createdAt']);

        $this->assertEquals($expectedData, $actualData);
    }

    /**
     * When not existing article is requested, error should be thrown.
     */
    public function testReadNonExistingArticle()
    {
        $this->client->request('GET', '/v1/articles/1234');
        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode(), $response->getContent());
        $this->assertContains('not found', $response->getContent());
    }
}
