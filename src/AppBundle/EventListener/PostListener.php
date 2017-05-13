<?php

namespace AppBundle\EventListener;

use AppBundle\Services\CurlRequest;
use Doctrine\DBAL\Exception as Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\AppBundleEvents;
use AppBundle\Event\PostEvent;

use AppBundle\Entity\Image;
use AppBundle\Entity\Location;
use AppBundle\Entity\Caption;
use AppBundle\Entity\Tag;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use AppBundle\Services\EntityManagement as EntityManagement;

class PostListener implements EventSubscriberInterface
{
    private $entityManagement;
    private $curl;

    public function __construct(EntityManagement $entityManagement, CurlRequest $curl = null)
    {
        $this->entityManagement = $entityManagement;
        $this->curl = $curl;
    }

    public static function getSubscribedEvents()
    {
        // Liste des évènements écoutés et méthodes à appeler
        return array(
            AppBundleEvents::ADD_POST_EVENT => 'contribute',
            AppBundleEvents::UPDATE_POST_VIEW_COUNT_EVENT => 'countUpdate',
            AppBundleEvents::ADD_COLLECTION_EVENT => 'contribute_collection',
            AppBundleEvents::UPDATE_COLLECTION_VIEW_COUNT_EVENT => 'collectionCountUpdate',
            KernelEvents::EXCEPTION => 'getKernelEvents'
        );
    }

    public function getKernelEvents(GetResponseForExceptionEvent $event){
        var_dump($event);
    }
    
    public function seoRewrite( $str, $utf8=true )
    {
        $str = (string)$str;
        if( is_null($utf8) ) {
            if( !function_exists('mb_detect_encoding') ) {
                $utf8 = (strtolower( mb_detect_encoding($str) )=='utf-8');
            } else {
                $length = strlen($str);
                $utf8 = true;
                for ($i=0; $i < $length; $i++) {
                    $c = ord($str[$i]);
                    if ($c < 0x80) $n = 0; # 0bbbbbbb
                    elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
                    elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
                    elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
                    elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
                    elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
                    else return false; # Does not match any model
                    for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
                        if ((++$i == $length)
                            || ((ord($str[$i]) & 0xC0) != 0x80)) {
                            $utf8 = false;
                            break;
                        }

                    }
                }
            }

        }

        if(!$utf8)
            $str = utf8_encode($str);
        $transliteration = array(
            'Ĳ' => 'I', 'Ö' => 'O','Œ' => 'O','Ü' => 'U','ä' => 'a','æ' => 'a',
            'ĳ' => 'i','ö' => 'o','œ' => 'o','ü' => 'u','ß' => 's','ſ' => 's',
            'À' => 'A','Á' => 'A','Â' => 'A','Ã' => 'A','Ä' => 'A','Å' => 'A',
            'Æ' => 'A','Ā' => 'A','Ą' => 'A','Ă' => 'A','Ç' => 'C','Ć' => 'C',
            'Č' => 'C','Ĉ' => 'C','Ċ' => 'C','Ď' => 'D','Đ' => 'D','È' => 'E',
            'É' => 'E','Ê' => 'E','Ë' => 'E','Ē' => 'E','Ę' => 'E','Ě' => 'E',
            'Ĕ' => 'E','Ė' => 'E','Ĝ' => 'G','Ğ' => 'G','Ġ' => 'G','Ģ' => 'G',
            'Ĥ' => 'H','Ħ' => 'H','Ì' => 'I','Í' => 'I','Î' => 'I','Ï' => 'I',
            'Ī' => 'I','Ĩ' => 'I','Ĭ' => 'I','Į' => 'I','İ' => 'I','Ĵ' => 'J',
            'Ķ' => 'K','Ľ' => 'K','Ĺ' => 'K','Ļ' => 'K','Ŀ' => 'K','Ł' => 'L',
            'Ñ' => 'N','Ń' => 'N','Ň' => 'N','Ņ' => 'N','Ŋ' => 'N','Ò' => 'O',
            'Ó' => 'O','Ô' => 'O','Õ' => 'O','Ø' => 'O','Ō' => 'O','Ő' => 'O',
            'Ŏ' => 'O','Ŕ' => 'R','Ř' => 'R','Ŗ' => 'R','Ś' => 'S','Ş' => 'S',
            'Ŝ' => 'S','Ș' => 'S','Š' => 'S','Ť' => 'T','Ţ' => 'T','Ŧ' => 'T',
            'Ț' => 'T','Ù' => 'U','Ú' => 'U','Û' => 'U','Ū' => 'U','Ů' => 'U',
            'Ű' => 'U','Ŭ' => 'U','Ũ' => 'U','Ų' => 'U','Ŵ' => 'W','Ŷ' => 'Y',
            'Ÿ' => 'Y','Ý' => 'Y','Ź' => 'Z','Ż' => 'Z','Ž' => 'Z','à' => 'a',
            'á' => 'a','â' => 'a','ã' => 'a','ā' => 'a','ą' => 'a','ă' => 'a',
            'å' => 'a','ç' => 'c','ć' => 'c','č' => 'c','ĉ' => 'c','ċ' => 'c',
            'ď' => 'd','đ' => 'd','è' => 'e','é' => 'e','ê' => 'e','ë' => 'e',
            'ē' => 'e','ę' => 'e','ě' => 'e','ĕ' => 'e','ė' => 'e','ƒ' => 'f',
            'ĝ' => 'g','ğ' => 'g','ġ' => 'g','ģ' => 'g','ĥ' => 'h','ħ' => 'h',
            'ì' => 'i','í' => 'i','î' => 'i','ï' => 'i','ī' => 'i','ĩ' => 'i',
            'ĭ' => 'i','į' => 'i','ı' => 'i','ĵ' => 'j','ķ' => 'k','ĸ' => 'k',
            'ł' => 'l','ľ' => 'l','ĺ' => 'l','ļ' => 'l','ŀ' => 'l','ñ' => 'n',
            'ń' => 'n','ň' => 'n','ņ' => 'n','ŉ' => 'n','ŋ' => 'n','ò' => 'o',
            'ó' => 'o','ô' => 'o','õ' => 'o','ø' => 'o','ō' => 'o','ő' => 'o',
            'ŏ' => 'o','ŕ' => 'r','ř' => 'r','ŗ' => 'r','ś' => 's','š' => 's',
            'ť' => 't','ù' => 'u','ú' => 'u','û' => 'u','ū' => 'u','ů' => 'u',
            'ű' => 'u','ŭ' => 'u','ũ' => 'u','ų' => 'u','ŵ' => 'w','ÿ' => 'y',
            'ý' => 'y','ŷ' => 'y','ż' => 'z','ź' => 'z','ž' => 'z','Α' => 'A',
            'Ά' => 'A','Ἀ' => 'A','Ἁ' => 'A','Ἂ' => 'A','Ἃ' => 'A','Ἄ' => 'A',
            'Ἅ' => 'A','Ἆ' => 'A','Ἇ' => 'A','ᾈ' => 'A','ᾉ' => 'A','ᾊ' => 'A',
            'ᾋ' => 'A','ᾌ' => 'A','ᾍ' => 'A','ᾎ' => 'A','ᾏ' => 'A','Ᾰ' => 'A',
            'Ᾱ' => 'A','Ὰ' => 'A','ᾼ' => 'A','Β' => 'B','Γ' => 'G','Δ' => 'D',
            'Ε' => 'E','Έ' => 'E','Ἐ' => 'E','Ἑ' => 'E','Ἒ' => 'E','Ἓ' => 'E',
            'Ἔ' => 'E','Ἕ' => 'E','Ὲ' => 'E','Ζ' => 'Z','Η' => 'I','Ή' => 'I',
            'Ἠ' => 'I','Ἡ' => 'I','Ἢ' => 'I','Ἣ' => 'I','Ἤ' => 'I','Ἥ' => 'I',
            'Ἦ' => 'I','Ἧ' => 'I','ᾘ' => 'I','ᾙ' => 'I','ᾚ' => 'I','ᾛ' => 'I',
            'ᾜ' => 'I','ᾝ' => 'I','ᾞ' => 'I','ᾟ' => 'I','Ὴ' => 'I','ῌ' => 'I',
            'Θ' => 'T','Ι' => 'I','Ί' => 'I','Ϊ' => 'I','Ἰ' => 'I','Ἱ' => 'I',
            'Ἲ' => 'I','Ἳ' => 'I','Ἴ' => 'I','Ἵ' => 'I','Ἶ' => 'I','Ἷ' => 'I',
            'Ῐ' => 'I','Ῑ' => 'I','Ὶ' => 'I','Κ' => 'K','Λ' => 'L','Μ' => 'M',
            'Ν' => 'N','Ξ' => 'K','Ο' => 'O','Ό' => 'O','Ὀ' => 'O','Ὁ' => 'O',
            'Ὂ' => 'O','Ὃ' => 'O','Ὄ' => 'O','Ὅ' => 'O','Ὸ' => 'O','Π' => 'P',
            'Ρ' => 'R','Ῥ' => 'R','Σ' => 'S','Τ' => 'T','Υ' => 'Y','Ύ' => 'Y',
            'Ϋ' => 'Y','Ὑ' => 'Y','Ὓ' => 'Y','Ὕ' => 'Y','Ὗ' => 'Y','Ῠ' => 'Y',
            'Ῡ' => 'Y','Ὺ' => 'Y','Φ' => 'F','Χ' => 'X','Ψ' => 'P','Ω' => 'O',
            'Ώ' => 'O','Ὠ' => 'O','Ὡ' => 'O','Ὢ' => 'O','Ὣ' => 'O','Ὤ' => 'O',
            'Ὥ' => 'O','Ὦ' => 'O','Ὧ' => 'O','ᾨ' => 'O','ᾩ' => 'O','ᾪ' => 'O',
            'ᾫ' => 'O','ᾬ' => 'O','ᾭ' => 'O','ᾮ' => 'O','ᾯ' => 'O','Ὼ' => 'O',
            'ῼ' => 'O','α' => 'a','ά' => 'a','ἀ' => 'a','ἁ' => 'a','ἂ' => 'a',
            'ἃ' => 'a','ἄ' => 'a','ἅ' => 'a','ἆ' => 'a','ἇ' => 'a','ᾀ' => 'a',
            'ᾁ' => 'a','ᾂ' => 'a','ᾃ' => 'a','ᾄ' => 'a','ᾅ' => 'a','ᾆ' => 'a',
            'ᾇ' => 'a','ὰ' => 'a','ᾰ' => 'a','ᾱ' => 'a','ᾲ' => 'a','ᾳ' => 'a',
            'ᾴ' => 'a','ᾶ' => 'a','ᾷ' => 'a','β' => 'b','γ' => 'g','δ' => 'd',
            'ε' => 'e','έ' => 'e','ἐ' => 'e','ἑ' => 'e','ἒ' => 'e','ἓ' => 'e',
            'ἔ' => 'e','ἕ' => 'e','ὲ' => 'e','ζ' => 'z','η' => 'i','ή' => 'i',
            'ἠ' => 'i','ἡ' => 'i','ἢ' => 'i','ἣ' => 'i','ἤ' => 'i','ἥ' => 'i',
            'ἦ' => 'i','ἧ' => 'i','ᾐ' => 'i','ᾑ' => 'i','ᾒ' => 'i','ᾓ' => 'i',
            'ᾔ' => 'i','ᾕ' => 'i','ᾖ' => 'i','ᾗ' => 'i','ὴ' => 'i','ῂ' => 'i',
            'ῃ' => 'i','ῄ' => 'i','ῆ' => 'i','ῇ' => 'i','θ' => 't','ι' => 'i',
            'ί' => 'i','ϊ' => 'i','ΐ' => 'i','ἰ' => 'i','ἱ' => 'i','ἲ' => 'i',
            'ἳ' => 'i','ἴ' => 'i','ἵ' => 'i','ἶ' => 'i','ἷ' => 'i','ὶ' => 'i',
            'ῐ' => 'i','ῑ' => 'i','ῒ' => 'i','ῖ' => 'i','ῗ' => 'i','κ' => 'k',
            'λ' => 'l','μ' => 'm','ν' => 'n','ξ' => 'k','ο' => 'o','ό' => 'o',
            'ὀ' => 'o','ὁ' => 'o','ὂ' => 'o','ὃ' => 'o','ὄ' => 'o','ὅ' => 'o',
            'ὸ' => 'o','π' => 'p','ρ' => 'r','ῤ' => 'r','ῥ' => 'r','σ' => 's',
            'ς' => 's','τ' => 't','υ' => 'y','ύ' => 'y','ϋ' => 'y','ΰ' => 'y',
            'ὐ' => 'y','ὑ' => 'y','ὒ' => 'y','ὓ' => 'y','ὔ' => 'y','ὕ' => 'y',
            'ὖ' => 'y','ὗ' => 'y','ὺ' => 'y','ῠ' => 'y','ῡ' => 'y','ῢ' => 'y',
            'ῦ' => 'y','ῧ' => 'y','φ' => 'f','χ' => 'x','ψ' => 'p','ω' => 'o',
            'ώ' => 'o','ὠ' => 'o','ὡ' => 'o','ὢ' => 'o','ὣ' => 'o','ὤ' => 'o',
            'ὥ' => 'o','ὦ' => 'o','ὧ' => 'o','ᾠ' => 'o','ᾡ' => 'o','ᾢ' => 'o',
            'ᾣ' => 'o','ᾤ' => 'o','ᾥ' => 'o','ᾦ' => 'o','ᾧ' => 'o','ὼ' => 'o',
            'ῲ' => 'o','ῳ' => 'o','ῴ' => 'o','ῶ' => 'o','ῷ' => 'o','А' => 'A',
            'Б' => 'B','В' => 'V','Г' => 'G','Д' => 'D','Е' => 'E','Ё' => 'E',
            'Ж' => 'Z','З' => 'Z','И' => 'I','Й' => 'I','К' => 'K','Л' => 'L',
            'М' => 'M','Н' => 'N','О' => 'O','П' => 'P','Р' => 'R','С' => 'S',
            'Т' => 'T','У' => 'U','Ф' => 'F','Х' => 'K','Ц' => 'T','Ч' => 'C',
            'Ш' => 'S','Щ' => 'S','Ы' => 'Y','Э' => 'E','Ю' => 'Y','Я' => 'Y',
            'а' => 'A','б' => 'B','в' => 'V','г' => 'G','д' => 'D','е' => 'E',
            'ё' => 'E','ж' => 'Z','з' => 'Z','и' => 'I','й' => 'I','к' => 'K',
            'л' => 'L','м' => 'M','н' => 'N','о' => 'O','п' => 'P','р' => 'R',
            'с' => 'S','т' => 'T','у' => 'U','ф' => 'F','х' => 'K','ц' => 'T',
            'ч' => 'C','ш' => 'S','щ' => 'S','ы' => 'Y','э' => 'E','ю' => 'Y',
            'я' => 'Y','ð' => 'd','Ð' => 'D','þ' => 't','Þ' => 'T','ა' => 'a',
            'ბ' => 'b','გ' => 'g','დ' => 'd','ე' => 'e','ვ' => 'v','ზ' => 'z',
            'თ' => 't','ი' => 'i','კ' => 'k','ლ' => 'l','მ' => 'm','ნ' => 'n',
            'ო' => 'o','პ' => 'p','ჟ' => 'z','რ' => 'r','ს' => 's','ტ' => 't',
            'უ' => 'u','ფ' => 'p','ქ' => 'k','ღ' => 'g','ყ' => 'q','შ' => 's',
            'ჩ' => 'c','ც' => 't','ძ' => 'd','წ' => 't','ჭ' => 'c','ხ' => 'k',
            'ჯ' => 'j','ჰ' => 'h', ' '=> '-', '!'=> ''
        );
        $str = str_replace( array_keys( $transliteration ),
            array_values( $transliteration ),
            $str);
        $str = strtolower($str);
        return $str;
    }

    public function contribute(PostEvent $event)
    {
        $post = $event->getPost();
        $media = $event->getMedia();

        if($media !== null){
            $this->fromInstagram($post, $media, $event);
        } else {
            if($post->getCaption() != "")
            {
                $caption = $post->getCaption();
                $caption->setText($this->hashTag($caption, $post));
            }

            if($post->getLocation() != ""){
                $location = $post->getLocation();
                $this->checkIs($post, $location);
            }
        }

    }
    
    public function countUpdate(PostEvent $event)
    {
        $post = $event->getPost();
        $this->updateView($post);
    }

    public function updateView($post){
        $newCount = $post->getView() + 1;
        $post->setView($newCount);
    }
    
    public function hashTag($caption, $post){

       $str = $caption->getText();
       preg_match_all("/(#\w+)/", $str, $matches, PREG_OFFSET_CAPTURE);

        foreach($matches[0] as $tag){
            $name = str_replace('#', "", $tag[0]);
            $existingTag = $this->entityManagement->rep('Tag')->findBy(array('name'=>$name));

            if(empty($existingTag)){
                $term = new Tag();
                $term->setName($name);
            } else {
                $term = $existingTag[0];
            }

            $post->addTag($term);
        }

       $regex = "/#+([a-zA-Z0-9_]+)/";
	   $str = preg_replace($regex, '<a href="#">$0</a>', $str);
        
	   return($str);
    }
    
    public function appendTag($post, $arrayTags){
        $content = $post->getDescription();
        foreach($arrayTags as $index=>$tag){
            $content.' #'.$tag->getName();    
        }
        $post->setDescription($content);
    }

    public function fromInstagram($post, $media, $event){

        $post->setIdInstagram($media['id']);
        $post->setCreated($media['created_time']);
        $post->setLink($media['link']);
        $post->setLikes($media['likes']['count']);
        //$post->setType($media['type']);
        $post->setIsInstagram(true);

        foreach($media['images'] as $index => $pic){
            $picted = $this->entityManagement->rep('Image')->findOneBy(array('url' => $pic['url']));
            if(empty($picted)){
                $image = new Image();
            } else {
                $image = $picted;
            }

            $image->setHeight($pic['height']);
            $image->setWidth($pic['width']);
            $image->setUrl($pic['url']);
            $image->setType($index);

            $post->setImage($image);

        }

        if(!empty($media['location']['id'])){
            $place = $this->entityManagement->rep('Location')->findOneBy(array('id_instagram' => $media['location']['id']));

            if($place === null){

                $location = new Location();
            } else {
                $location = $place;
            }

            $location->setIdInstagram($media['location']['id']);
            $location->setLatitude($media['location']['latitude']);
            $location->setLongitude($media['location']['longitude']);
            $location->setName($media['location']['name']);
            $location->addPost($post);

            $post->setLocation($location);
        }

        if(!empty($media['caption']['id'])){
            $description = $this->entityManagement->rep('Caption')->findOneBy(array('idInstagram' => $media['caption']['id']));
            if(null === $description){
                $caption = new Caption();
            } else {
                $caption = $description;
            }
            $caption->setIdInstagram($media['caption']['id']);

            $str = $media['caption']['text'];
            preg_match_all("/(#\w+)/", $str, $matches, PREG_OFFSET_CAPTURE);
            $regex = "/#+([a-zA-Z0-9_]+)/";
            $str = preg_replace($regex, '<a href="#">$0</a>', $str);
            $caption->setText($str);
            $caption->setCreated($media['caption']['created_time']);

            $post->setCaption($caption);
        }

        if(!empty($media['tags'])){
            foreach($media['tags'] as $hash){

                $word = $this->entityManagement->rep('Tag')->findOneBy(array('name' => $hash));
                if(empty($word)){
                    $tag = new Tag();
                    $tag->setName($hash);
                    $post->addTag($tag);
                }

            }
        }
    }

    public function checkIs($post, $location){
        $name = $location->getName();
        $existingRecord = $this->entityManagement->rep('Location')->findBy(array('name'=>$name));

        if(empty($existingRecord)){

            $place = new Location();
            $place->setName($name);

            $coordinates = $this->getCoordinates($location);
            $place->setLatitude($coordinates['lat']);
            $place->setLongitude($coordinates['lng']);

        } else {
            $place = $existingRecord[0];
        }

        $post->setLocation($place);
    }

    public function getCoordinates($location){
        $access_token = 'AIzaSyCVRvGk10nM0kYhdV_lJye16kv2hgTe6LE';
        $place = rawurlencode($location->getName());
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$place.'&key='.$access_token;

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

        return $response['results'][0]['geometry']['location'];

    }

    function getUsersHadLiked($media_id){
        $access_token = $this->getParameter('instagram_key');
        $url = 'https://api.instagram.com/v1/media/'.$media_id.'/likes?access_token='.$access_token;

        $content = $this->curkl->createCurl($url);

    }
}