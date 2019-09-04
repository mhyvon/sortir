<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/participant")
 */
class ParticipantController extends Controller
{
    /**
     * @Route("/", name="participant_index", methods={"GET","POST"})
     */
    public function index(ParticipantRepository $repository){
        return $this->render('participant/index.html.twig', [
            'participants'=>$repository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/desactiver", name="participant_desactiver", methods={"GET","POST"})
     * @param Participant $participant
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function desactiver(Participant $participant, EntityManagerInterface $em){
        $participant->setActif(false);
        $em->persist($participant);
        $em->flush();

        return $this->redirectToRoute('participant_index');
    }

    /**
     * @Route("/{id}/activer", name="participant_activer", methods={"GET","POST"})
     * @param Participant $participant
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function activer(Participant $participant, EntityManagerInterface $em){
        $participant->setActif(true);
        $em->persist($participant);
        $em->flush();

        return $this->redirectToRoute('participant_index');
    }

    /**
     * @Route("/show/{id}/{sortie}", name="participant_show", methods={"GET","POST"})
     * @param Participant $participant
     * @param Sortie $sortie
     * @return Response
     */
    public function show(Participant $participant, Sortie $sortie): Response
    {

        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
            'sortie' => $sortie,
        ]);
    }









}