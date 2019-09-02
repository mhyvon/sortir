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



    public function rechercheSortie($var, $site, $dateD, $dateF, $orga, $inscr, $nonInscr, $passe, $connecte) : array
    {

        $req = $this->createQueryBuilder('s');

        // Recherche par site
        if($site){

            // TODO : something

        }

        // Recherche par mot-clé titre/description
        if($var){
            $req
                ->andWhere('s.nom like :var')
                ->orWhere('s.description like :var')
                ->setParameter('var','%'.$var.'%')
                ->setMaxResults(6);
        }

        // Recherche par date
        if($dateD && $dateF){
            $req
                ->andWhere('s.debut BETWEEN :min AND :max')
                ->setParameter('min', $dateD)
                ->setParameter('max', $dateF)
            ;
        }

        // Check si l'utilisateur connecté est l'organisateur
        if($orga){
            $req
                ->andWhere('s.organisateur = :orga')
                ->setParameter('orga', $connecte);
        }

        // Check si la sortie est passée
        if($passe){
            $req
                ->andWhere('DATE_ADD(s.duree, s.debut, \'minute\') <= CURRENT_DATE()');
        }

        // Check si l'utilisateur connecté est inscrit
        if($inscr){
            $req
                ->andWhere(':user MEMBER OF s.inscriptions')
                ->setParameter('user', $connecte);
            // sortie_participant as sp on sp.sortie_id = s.sortie_id where sp.participant_id =$connecte->getId();
        }

        // Check si l'utilisateur connecté est inscrit
        if($nonInscr){
            $req
                ->andWhere(':user NOT MEMBER OF s.inscriptions')
                ->setParameter('user', $connecte);
        }

        //$req->setMaxResults(6);

        return $req->getQuery()->getResult();
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
