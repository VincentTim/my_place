<?php

namespace AppBundle\Controller;

use InstagramAPI\Instagram;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Post;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $posts = $this->get('entity.management')->rep('Post')->findAll();
        return $this->render('default/index.html.twig', array(
            'posts' => $posts
        ));
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(){

        //https://api.instagram.com/v1/users/self/media/recent/?limit=70&access_token=1532167946.1b47213.964c653517c446bf899d3067c321a110

        $uri = 'https://api.instagram.com/v1/';
        $endpoint = 'users/self/media/recent/';
        $token = '1532167946.1b47213.964c653517c446bf899d3067c321a110';

        // Get cURL resource
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_VERBOSE => true,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $uri . $endpoint . $token,
            CURLOPT_USERAGENT => 'Codular Sample cURL Request',
            CURLOPT_SSL_VERIFYPEER => false
        ));
// Send the request & save response to $resp
        $resp = curl_exec($curl);
        var_dump($resp);
        echo curl_error($curl);
// Close request to clear up some resources
        curl_close($curl);

        return new Response(1);

    }
}
