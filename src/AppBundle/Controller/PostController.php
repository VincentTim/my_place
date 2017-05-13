<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use AppBundle\AppBundleEvents;
use AppBundle\EventListener\PostListener;

use Symfony\Component\EventDispatcher\EventDispatcher;

use AppBundle\Event\PostEvent;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use AppBundle\Entity\Image;
use AppBundle\Form\ImageType;

use AppBundle\Services\EntityManagement as EntityManagement;

class PostController extends Controller
{
    /**
     * @Route("/instagram/import", name="post_import_instagram_content")
     */
    public function contribute(){
        $access_token = $this->getParameter('instagram_key');
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

            $event = new PostEvent($post, $request, $media);
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

        return $this->render('default/index.html.twig', array(
            'posts' => $posted
        ));

        return new Response(1);
    }

    /**
     * @Route("/add/post", name="post_form_image")
     */
    public function addImage(Request $request){
        $form = $this->createForm(new PostType());
        $formImage = $this->createForm(new ImageType());

        $image = new Image();

        if($request->getMethod() == 'POST') {

            $formImage->handleRequest($request);

            if ($formImage->isValid()) {
                $image = $formImage->getData();
                $image->upload();
                if(!$this->get('entity.management')->add($image)){
                    print_r($formImage->getErrorsAsString());
                } else {

                };
                return $this->redirect($this->generateUrl('post_crop', array('id' => $image->getId())));
            }
        } else {
            return $this->render('default/post.html.twig', array(
                'formPost' => $form->createView(),
                'formImage' => $formImage->createView()
            ));
        }



    }

    /**
     * @Route("/add/crop/{id}", name="post_crop")
     */
    public function cropImage($id, Request $request){

        $image = $this->get('entity.management')->rep('Image')->find($id);

        return $this->render('default/crop.html.twig', array(
            'image' => $image,
            'id' => $id
        ));
    }

    /**
     * @Route("/add/content", name="post_form_content")
     */
    public function addPost(Request $request){

        $data = $request->request->all();

        $image = $this->get('entity.management')->rep('Image')->find($data['id']);

        $post = new Post();

        $form = $this->createForm(new PostType(), $post, array('image' => $image, 'action' => $this->generateUrl('post_form_contribute')));

        if($request->getMethod() == 'POST') {

                $form->handleRequest($request);

                if ($form->isValid()) {

                    $post = $form->getData();

                    if (!$this->get('entity.management')->add($post)) {
                        print_r($form->getErrorsAsString());
                    }

                }


                if(!empty($data) && isset($data['image'])){
                    $this->crop($data['image'], $data['w'], $data['h'], $data['x'], $data['y'], __DIR__ . '/../../../web' . $data['folder']);
                }


        }



        return $this->render('default/post_content.html.twig', array(
            'formPost' => $form->createView(),
            'image' => $data['image'],
            'imageId' => $image
        ));



    }

    /**
     * @Route("/add/content/post", name="post_form_contribute")
     */
    public function contributePost(Request $request){

        $dispatcher = new EventDispatcher();
        $subscriber = new PostListener($this->get('entity.management'));
        $dispatcher->addSubscriber($subscriber);

        $post = new Post();
        $media = array();

        $form = $this->createForm(new PostType(), $post);

        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);

            if($form->isValid()){

                $post = $form->getData();



                $event = new PostEvent($post, $request);
                $dispatcher->dispatch(AppBundleEvents::ADD_POST_EVENT, $event);

                if (null === $response = $event->getResponse()) {

                    if ($event->getPost()->getId() != null) {
                        $this->get('entity.management')->update($post);
                    } else {
                        $this->get('entity.management')->add($post);
                    }

                    $response = $this->redirect($this->generateUrl('post_list'));

                    return $response;

                }
            } else {
                var_dump($form->getErrorsAsString());
                exit;
            }
        }
    }

    private function crop($image, $width, $height, $x, $y, $dossier)
    {

        $source = $dossier.'standard_resolution/'.$image;
        $destination = $dossier.'low_resolution/'.$image;

        $size = getimagesize($source);

        switch ($size['mime']) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                $quality = 100;
                break;

            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
                $quality = 9;
                break;

            default:
                throw new Exception('Unknown image type.');
        }

            $targ_w = $targ_h = 640;

            copy($source, $destination);

            $img_r = $image_create_func($destination);
            $dst_r = imagecreatetruecolor( $targ_w, $targ_h );


            imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$width,$height);


        $image_save_func($dst_r , $destination, $quality);




     }
}
