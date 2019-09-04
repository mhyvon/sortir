<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/participant")
 */
class ParticipantController extends Controller
{

    /**
     * @Route("/{id}/{sortie}", name="participant_show", methods={"GET","POST"})
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