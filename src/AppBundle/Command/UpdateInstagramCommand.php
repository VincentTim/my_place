<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\AppBundleEvents;
use AppBundle\EventListener\PostListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use AppBundle\Event\PostEvent;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Post;

class UpdateInstagramCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:update-instagram')

            // the short description shown while running "php bin/console list"
            ->setDescription('Update database with instagram records.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user...');
// ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Update of instagram records',
            '============',
            '',
        ]);


        $access_token = $this->getContainer()->getParameter('instagram_key');
        $url = 'https://api.instagram.com/v1/users/self/media/recent/?access_token='.$access_token;
        $curlManager = $this->getContainer()->get('curl.request');
        //$content = $curlManager->createCurl($url);

        $content = file_get_contents(__DIR__.'/../Service/instagram.export.json');
        $response = json_decode($content, true);

        $medias = $response['data'];

        $request = new Request();

        $dispatcher = new EventDispatcher();
        $subscriber = new PostListener($this->getContainer()->get('entity.management'));
        $dispatcher->addSubscriber($subscriber);


        foreach($medias as $media){

            $posted = $this->getContainer()->get('entity.management')->rep('Post')->findOneBy(array('id_instagram' => $media['id']));
            if(!empty($posted)){
                $post = $posted;
            } else {
                $post = new Post();
            }

            $event = new PostEvent($post, $request, $media);
            $dispatcher->dispatch(AppBundleEvents::ADD_POST_EVENT, $event);

            if (null === $response = $event->getResponse()) {

                if ($event->getPost()->getId() != null) {
                    $this->getContainer()->get('entity.management')->update($post);
                    $output->writeln($media['id'].' : update');
                } else {
                    $this->getContainer()->get('entity.management')->add($post);
                    $output->writeln($media['id'].' : create');
                }

            }
        }

        exec('gulp default');

        // outputs a message without adding a "\n" at the end of the line
        $output->write('You are about to ');
        $output->write('create a user.');
// ...
    }
}

?>