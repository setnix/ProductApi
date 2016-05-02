<?php


namespace AppBundle\Tests\Functional;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use AppBundle\Test\FunctionalTestCase;

/**
 * Test Article create cases.
 *
 * Class CreateArticleTest
 */
class CreateArticleTest extends FunctionalTestCase
{
    /**
     * Article should be created when valid data is provided
     */
    public function testArticleCreateWithValidData()
    {
        $params = [
            'article' => [
                'title' => 'Test product title',
                'body'  => 'Product description',
                'tags'  => [
                    ['name' => 'tag 1'],
                    ['name' => 'tag 2'],
                ],
            ],
        ];

        $expectedData = [
            'title' => $params['article']['title'],
            'body'  => $params['article']['body'],
            'tags'  => $params['article']['tags'],
        ];

        $this->client->request('POST', '/v1/articles', $params);
        $response = $this->client->getResponse();

        $this->assertEquals(201, $response->getStatusCode(), $response->getContent());

        $actualData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('createdAt', $actualData);
        $this->assertArrayHasKey('id', $actualData);
        unset($actualData['id']);
        unset($actualData['createdAt']);

        $this->assertEquals($expectedData, $actualData);
    }

    /**
     * When creating article with title missing, error should be thrown
     */
    public function testArticleCreateWithTitleMissing()
    {
        $params = [
            'article' => [
                'body' => 'Product description',
                'tags' => [
                    ['name' => 'tag 1'],
                    ['name' => 'tag 2'],
                ],
            ],
        ];

        $this->client->request('POST', '/v1/articles', $params);
        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode(), $response->getContent());
        $this->assertContains('title must be defined', $response->getContent());
    }

    /**
     * When creating article with tag's name missing, error should be thrown
     */
    public function testArticleCreateWithTagNameMissing()
    {
        $params = [
            'article' => [
                'title' => 'Product title',
                'body' => 'Product description',
                'tags' => [
                    ['title' => 'tag 1']
                ],
            ],
        ];

        $this->client->request('POST', '/v1/articles', $params);
        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode(), $response->getContent());
        $this->assertContains('Tag name must be defined', $response->getContent());
    }

    /**
     * When creating article with tags missing, error should be thrown
     */
    public function testArticleCreateWithTagsMissing()
    {
        $params = [
            'article' => [
                'title' => 'Product title',
                'body'  => 'Product description',
            ],
        ];

        $this->client->request('POST', '/v1/articles', $params);
        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode(), $response->getContent());
        $this->assertContains('must have at least one tag', $response->getContent());
    }

    /**
     * When creating article with same tags, existing tags should be re-used
     */
    public function testWhenCreatingArticleWithSameTagsExistingTagsShoudBeUsed()
    {
        // Create tag
        $tag = new Tag();
        $tag->setName('existing tag');
        $this->getEntityManager()->persist($tag);
        $this->getEntityManager()->flush();

        $params = [
            'article' => [
                'title' => 'Test product title',
                'body'  => 'Product description',
                'tags'  => [
                    [
                        'id' => $tag->getId(),
                        'name' => 'existing tag'
                    ],
                ],
            ],
        ];

        $this->client->request('POST', '/v1/articles', $params);
        $response = $this->client->getResponse();

        $this->assertEquals(201, $response->getStatusCode(), $response->getContent());

        $createdArticle = $this->getEntityManager()->getRepository('AppBundle:Article')->findOneBy(
            ['title' => $params['article']['title']]
        );

        $this->assertTrue($createdArticle->getTags()->contains($tag));
    }
}
