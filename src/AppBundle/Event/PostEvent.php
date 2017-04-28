<?php

namespace AppBundle\Event;

use AppBundle\Entity\Post;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostEvent extends Event
{
    private $request;
    private $post;

    /**
     * PostEvent constructor.
     * @param Post $post
     * @param Request $request
     */
    public function __construct(Post $post, Request $request)
    {
        $this->post = $post;
        $this->request = $request;
    }

    /**
     * @param $post
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
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