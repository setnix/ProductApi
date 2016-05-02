<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ApiController
 */
class ApiController extends FOSRestController
{
    /**
     * Create response with provided status code
     *
     * @param mixed   $responseData
     * @param integer $statusCode
     *
     * @return Response
     */
    protected function createResponse($responseData, $statusCode = Response::HTTP_OK)
    {
        $view = $this->view($responseData);

        $response = $this->handleView($view);
        $response->setStatusCode($statusCode);

        return $response;
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

    /**
     * Persist entity to data store.
     *
     * @param object $entity
     */
    protected function persist($entity)
    {
        if (!is_object($entity)) {
            throw new HttpException(500, 'Entity should be provided');
        }

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->persist($entity);
        $entityManager->flush();
    }
}
