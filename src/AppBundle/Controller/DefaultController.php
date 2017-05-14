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
     * @Route("/post/{id}", name="post_detail")
     */
    public function modalAction($id=null){

        $post = $this->get('entity.management')->rep('Post')->find($id);
        return $this->render('default/ajax-detail.html.twig', array(
            'post' => $post
        ));
    }

    /**
     * @param $tag
     * @return Response
     * @Route("/explore/{tag}", name="post_explore")
     */
    public function exploreAction($tag){
      return new Response($tag);
    }

    /**
     * @Route("/generate-classes", name="post_classes")
     */
    public function createClass(){

        $posts = $this->get('entity.management')->rep('Post')->findAll();

        $file = __DIR__.'/../../../app/Resources/assets/styles/_classes.scss';
        $current = file_get_contents($file);

        $current .= '.content {';
        foreach($posts as $post){

            $current .= '&-c'.$post->getId().' {';
            if(!empty($post->getIsInstagram())){
                $current .= 'background-image: url("'.$post->getImage()->getUrl().'")';
            } else {
                $current .= 'background-image: url($path-img + "'.$post->getImage()->getName().'")';
            }

            $current .= '}';
        }
        $current .= '}';

        file_put_contents($file, $current);

        return new Response(1);
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
