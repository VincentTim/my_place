<?php

namespace AppBundle\Event;

use AppBundle\Entity\Collection;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectionEvent extends Event
{
    private $request;
    private $collection;

    /**
     * PostEvent constructor.
     * @param Post $collection
     * @param Request $request
     */
    public function __construct(Collection $collection, Request $request)
    {
        $this->collection = $collection;
        $this->request = $request;
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @var Response
     */
    private $response;
    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }


}