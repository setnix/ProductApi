<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;
use AppBundle\Repository\ArticleRepository;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ArticleController
 */
class ApiArticleController extends ApiController
{
    /**
     * Bulk read of articles.
     *
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Limit records.")
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Offset for records.")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     */
    public function indexAction(ParamFetcher $paramFetcher)
    {
        $limit      = $paramFetcher->get('limit');
        $offset     = $paramFetcher->get('offset');
        $results    = $this->getArticleRepository()->findBy([], [], $limit, $offset);

        return $this->createResponse($results);
    }

    /**
     * Show single article.
     *
     * @param integer $articleId
     *
     * @return Response
     */
    public function showAction($articleId)
    {
        $entity = $this->getArticle($articleId);

        return $this->createResponse($entity);
    }

    /**
     * Create new article.
     *
     * @return Response
     */
    public function createAction()
    {
        $form    = $this->createForm(ArticleType::class, null);
        $request = $this->get('request_stack')->getCurrentRequest();

        $form->handleRequest($request);
        $this->validateRequest($form);

        $resource = $form->getData();

        $this->persist($resource);

        return $this->createResponse($resource, Response::HTTP_CREATED);
    }

    /**
     * Update existing article.
     *
     * @param integer $articleId
     *
     * @return Response
     */
    public function updateAction($articleId)
    {
        $entity = $this->getArticle($articleId);

        $request = $this->get('request_stack')->getCurrentRequest();
        $form    = $this->createForm(ArticleType::class, $entity, ['method' => $request->getMethod()]);

        $form->handleRequest($request);
        $this->validateRequest($form);

        $resource = $form->getData();

        $this->persist($resource);

        return $this->createResponse($resource);
    }

    /**
     * Delete article.
     *
     * @param integer $articleId
     *
     * @return Response
     */
    public function deleteAction($articleId)
    {
        $entity = $this->getArticle($articleId);

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->remove($entity);
        $entityManager->flush($entity);

        return $this->createResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Get article from data store.
     *
     * @param integer $articleId
     *
     * @return Article
     * @throws NotFoundHttpException
     */
    protected function getArticle($articleId)
    {
        $repository = $this->getArticleRepository();
        $entity     = $repository->find($articleId);

        if (empty($entity)) {
            throw $this->createNotFoundException('Article not found');
        }

        return $entity;
    }

    /**
     * Persist entity to data store.
     *
     * @param Article $entity
     */
    protected function persist(Article $entity)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    /**
     * Get article repository.
     *
     * @return ArticleRepository
     */
    protected function getArticleRepository()
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');

        return $entityManager->getRepository('AppBundle:Article');
    }

    /**
     * Validate form.
     *
     * @param FormInterface $form
     */
    protected function validateRequest(FormInterface $form)
    {
        if (!$form->isValid()) {
            throw new HttpException(400, $form->getErrors(true, false));
        }
    }
}
