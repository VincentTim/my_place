<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;

use AppBundle\AppBundleEvents;
use AppBundle\EventListener\PostListener;

use AppBundle\Event\PostEvent;
use AppBundle\Event\CollectionEvent;


class ItemsFunctions extends Controller
{


    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $post
     */
    public function countUpdate($post){

        $request = new Request();
        $dispatcher = new EventDispatcher();

        $subscriber = new PostListener();
        $dispatcher->addSubscriber($subscriber);

        if(null !== $post->getEntityName()){
            $event = new CollectionEvent($post, $request);
            $dispatcher->dispatch(AppBundleEvents::UPDATE_COLLECTION_VIEW_COUNT_EVENT, $event);
        } else {
            $event = new PostEvent($post, $request);
            $dispatcher->dispatch(AppBundleEvents::UPDATE_POST_VIEW_COUNT_EVENT, $event);
        }

        if (null === $response = $event->getResponse()) {

            $this->entityManager->update($post);

        }



    }

    /**
     * @param $id
     * @return mixed
     */
    public function randomPost($id){

        $posts = $this->entityManager->rep('Post')->findBy(array('publish'=>true));

        $arrayPost =  array();

        foreach($posts as $index => $post){
            $arrayPost[$index] = $post->getId();
        }

        $index = array_rand ( $arrayPost );

        $interval = $arrayPost[$index];

        if($interval == $id){
            $index == end($arrayPost);
            $random = $this->entityManager->rep('Post')->find(prev($arrayPost));
        } else {
            $random = $this->entityManager->rep('Post')->find($arrayPost[$index]);
        }

        return $random;
    }

    /**
     * @return mixed
     */
    public function mostView(){
        $most = $this->entityManager->rep('Post')->getMostViewed();
        return $most;
    }
}