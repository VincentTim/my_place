<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class EntityManagement extends Controller
{
	
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    public function __construct($entity, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $entity
     * @return mixed
     *
     * Récupération d'une entité
     */
    public function rep($entity)
    {
        return $this->entityManager->getRepository('AppBundle:' . $entity);
    }

    /**
     * @param $entity
     * Méthode ajout
     */
    public function add($entity)
    {
        $em = $this->entityManager;
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param $entity
     * Méthode de mise à jour
     */
    public function update($entity)
    {
        $em = $this->entityManager;
        $em->flush();

    }

    /**
     * @param $entity
     * Méthode de suppression
     */
    public function delete($entity)
    {
        $em = $this->entityManager;
        $em->remove($entity);
        $em->flush();
    }


}
