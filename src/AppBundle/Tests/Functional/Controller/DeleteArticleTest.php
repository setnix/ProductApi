<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Tag;
use AppBundle\Test\FunctionalTestCase;

/**
 * Test Article delete cases
 *
 * Class DeleteArticleTest
 */
class DeleteArticleTest extends FunctionalTestCase
{
    /**
     * When article is deleted status code 204 and no content should be returned.
     */
    public function testDeleteExistingArticle()
    {
        $tag = new Tag();
        $tag->setName('tag 1');

        $article = new Article();
        $article->setTitle('Product 1');
        $article->setBody('Body text 1');
        $article->addTag($tag);

        $this->populateDataBaseWithSampleData([$article]);

        $articleId = $article->getId();

        $this->client->request('DELETE', '/v1/articles/' . $articleId);
        $response = $this->client->getResponse();

        $this->assertEquals(204, $response->getStatusCode(), $response->getContent());
        $this->assertEmpty($response->getContent(), $response->getContent());
    }

    /**
     * When deleting not existing article, error should be thrown.
     */
    public function testReadNonExistingArticle()
    {
        $this->client->request('DELETE', '/v1/articles/1234');
        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode(), $response->getContent());
        $this->assertContains('not found', $response->getContent());
    }
}
