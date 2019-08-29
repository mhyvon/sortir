<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends Controller
{
    /**
     * @Route("/", name="sortie_index", methods={"GET"})
     */
    public function index(SortieRepository $sortieRepository): Response
    {
        $liste = $sortieRepository->findAll();
        foreach ($liste as $sortie) {
            $this->maj($sortie);
        };

        return $this->render('sortie/index.html.twig', [
            'sorties' => $liste,
        ]);
    }

    /**
     * @Route("/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request, EtatRepository $repository): Response
    {
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());

        $etat = $repository->findOneBy(['libelle'=>'Créée']);
        $sortie->setEtat($etat);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/register", name="sortie_register", methods={"GET","POST"})
     * @param Sortie $sortie
     * @return RedirectResponse
     */
    public function addParticipant(Sortie $sortie){

        $sortie->addInscription($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_show',  [
            'id'=>$sortie->getId(),
        ]);
    }

    /**
     * @param Sortie $sortie
     * @Route("/{id}/unregister", name="sortie_unregister", methods={"GET","POST"})
     * @return RedirectResponse
     */
    public function removeParticipant(Sortie$sortie){

        $sortie->removeInscription($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_show',  [
            'id'=>$sortie->getId(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        $this->maj($sortie);
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Sortie $sortie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_index');
    }

    public function maj(Sortie $sortie){

        $em = $this->getDoctrine()->getManager();

        $now = new \DateTime('now');
        $repo = $this->getDoctrine()->getRepository(Etat::class);

        $dateFin = new \DateTime($sortie->getDebut()->format('Y-m-d H:i:s'));

        $dateFin->add(new \DateInterval('PT' . $sortie->getDuree() . 'M'));

        if ($now>$sortie->getDebut()&&$now<$dateFin) {
            $etat = $repo->findOneBy(['libelle'=>'Activité en cours']);
            $sortie->setEtat($etat);
        }
        if ($now>$dateFin&&$sortie->getEtat()->getLibelle()!='Annulée'){
            $etat = $repo->findOneBy(['libelle'=>'Passée']);
            $sortie->setEtat($etat);
        }
        $em->persist($sortie);
        $em->flush();
    }
}
