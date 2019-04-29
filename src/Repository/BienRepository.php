<?php

namespace App\Repository;

use App\Entity\Bien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Bien|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bien|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bien[]    findAll()
 * @method Bien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BienRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bien::class);
    }
    
    //use in home pour requete ajax
    public function findAllBiensByCriteres($criteres = []) {
        $qb = $this->createQueryBuilder('b');
               
        if ($criteres['prix']) {
            $qb->andWhere('b.prix < :prix')
                    ->setParameter(':prix', $criteres['prix']);
        }
        if ($criteres['nombreDePieces']) {
            $qb->andWhere('b.nombreDePieces > :nombreDePieces')
                    ->setParameter(':nombreDePieces', $criteres['nombreDePieces']);
        }
        
        return $qb->getQuery()->getResult();
        //dump($qb->getQuery()); die();
    }
    

    // /**
    //  * @return Bien[] Returns an array of Bien objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bien
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
