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
    private $media;

    /**
     * PostEvent constructor.
     * @param Post $post
     * @param array() $media
     * @param Request $request
     */
    public function __construct(Post $post, $media, Request $request)
    {
        $this->post = $post;
        $this->media = $media;
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
     * @param $media
     * @return mixed
     */
    public function getMedia()
    {
        return $this->media;
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