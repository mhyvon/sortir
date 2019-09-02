<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }



    public function rechercheSortie($var, $site, $dateD, $dateF, $orga, $inscr, $nonInscr, $passe) : array
    {



        if($site){
            $req = $this->createQueryBuilder('s')
                ->where('s.nom like :var')
                ->setParameter('var','%'.$var.'%')
                ->setMaxResults(6)
            ;
        }
        if($var && $site==null){
            $req = $this->createQueryBuilder('s')
                ->where('s.nom like :var')
                ->orWhere('s.description like :var')
                ->setParameter('var','%'.$var.'%')
                ->setMaxResults(6)
            ;
        }
            else{
                $req = $this->createQueryBuilder('s')
                    ->where('s.nom like :var')
                    ->orWhere('s.description like :var')
                    ->setParameter('var','%'.$var.'%')
                    ->setMaxResults(6)
                ;
            }



        return $req->getQuery()->getResult();
        exit();
    }


    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
