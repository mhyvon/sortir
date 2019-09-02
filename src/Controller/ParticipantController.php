<?php


namespace App\Controller;


use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantController extends Controller
{

    /**
     * @Route("/{id}", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {

        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }









}