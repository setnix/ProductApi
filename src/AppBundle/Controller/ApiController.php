<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

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
}
