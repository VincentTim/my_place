<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use AppBundle\Form\CaptionType;
use AppBundle\Form\LocationType;

class PostType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($options['action'])
            /*->add('mime', EntityType::class, array(
                'class' => 'AppBundle:Mime',
                'property' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');

                    return $qb;
                },
            ))
            ->add('tag', EntityType::class, array(
                'class' => 'AppBundle:Tag',
                'property' => 'name',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');

                    return $qb;
                },
            ))*/
            ->add('caption', CaptionType::class)
            ->add('location', LocationType::class)
            ->add('image', EntityType::class, array(
                'class' => 'AppBundle:Image',
                'property' => 'name',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('u');
                    if($options['image'] !== null){
                        $qb->where('u.id = :image');
                        $qb->setParameter('image', $options['image']->getId()) ;
                    }

                        $qb->orderBy('u.name', 'ASC');

                    return $qb;
                },
            ))
            ->add('save', 'submit')
            ->getForm()
            /*->add('location')
            ->add('caption')
            */;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Post',
            'image' => null,
            'action' => ''
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'post';
    }


}
