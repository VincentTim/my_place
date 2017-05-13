<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class FileResize
{


    function resize($newWidth, $newHeight, $targetFile, $originalFile) {

        $info = getimagesize($originalFile);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                $new_image_ext = 'jpg';
                break;

            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
                $new_image_ext = 'png';
                break;

            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
                $new_image_ext = 'gif';
                break;

            default:
                throw new Exception('Unknown image type.');
        }

        $img = $image_create_func($originalFile);
        list($width, $height) = getimagesize($originalFile);

        //IMAGE PAYSAGE
        if(null === $newWidth){
            //3000*2000
            //    *640
            $distHeight = $newHeight;
            $distWidth = ($newHeight * $width) / $height;
        }
        //IMAGE PORTRAIT
        if(null === $newHeight){
            //2000*3000
            //640
            $distWidth = $newWidth;
            $distHeight = ($newWidth * $height) / $width;
        }


        $tmp = imagecreatetruecolor($distWidth, $distHeight);

        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $distWidth, $distHeight, $width, $height);

        if (file_exists($targetFile)) {
            unlink($targetFile);
        }
        if(!$image_save_func($tmp, "$targetFile")){
            echo 'failed';
            die();
        };
    }



}