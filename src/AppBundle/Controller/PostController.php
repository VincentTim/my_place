<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use AppBundle\AppBundleEvents;
use AppBundle\EventListener\PostListener;

use Symfony\Component\EventDispatcher\EventDispatcher;

use AppBundle\Event\PostEvent;

use AppBundle\Entity\Post;

class PostController extends Controller
{
    /**
     * @Route("/instagram/import", name="post_import_instagram_content")
     */
    public function contribute(){

        $access_token = '1532167946.1b47213.964c653517c446bf899d3067c321a110';
        $url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token='.$access_token;

        $options = array(
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;

        $response = json_decode($content, true);
        $medias = $response['data'];

        $request = new Request();

        $dispatcher = new EventDispatcher();
        $subscriber = new PostListener($this->get('entity.management'));
        $dispatcher->addSubscriber($subscriber);


        foreach($medias as $media){

            $posted = $this->get('entity.management')->rep('Post')->findOneBy(array('id_instagram' => $media['id']));
            if(!empty($posted)){
                $post = $posted;
            } else {
                $post = new Post();
            }

            $event = new PostEvent($post, $media, $request);
            $dispatcher->dispatch(AppBundleEvents::ADD_POST_EVENT, $event);

            if (null === $response = $event->getResponse()) {

                if ($event->getPost()->getId() != null) {
                    $this->get('entity.management')->update($post);
                } else {
                    $this->get('entity.management')->add($post);

                }

            }
        }

        $response = $this->redirectToRoute('post_list', array(), 301);
        return $response;


    }

    /**
     * @Route("/instagram", name="post_list")
     */
    public function postList(){
        $posted = $this->get('entity.management')->rep('Post')->findAll();
        var_dump(count($posted));
        return new Response(1);
    }
}
