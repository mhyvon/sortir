<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/lieu")
 */
class LieuController extends Controller
{
    /**
     * @Route("/", name="lieu_index", methods={"GET"})
     */
    public function index(LieuRepository $lieuRepository): Response
    {
        return $this->render('lieu/index.html.twig', [
            'lieus' => $lieuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajaxModale", name="lieu_ajaxModale", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function ajaxModale(Request $request, EntityManagerInterface $em, VilleRepository $repository) {

        $formData = $request->request->all();

        $nom = $formData['lieu']['nom'];
        $rue = $formData['lieu']['rue'];
        $longitude = (float)$formData['lieu']['longitude'];
        $latitude = (float)$formData['lieu']['latitude'];
        $ville=$repository->find($formData['lieu']['ville']);

        if ($nom && $rue && $ville) {
            $lieu = new Lieu();
            $lieu->setNom($nom)
                ->setRue($rue)
                ->setLatitude($latitude)
                ->setLongitude($longitude)
                ->setVille($ville);

            $em->persist($lieu);
            $em->flush();
        }

        return new Response();
    }

    /**
     * @Route("/new", name="lieu_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('lieu_index');
            //return new Response();
        }

        return $this->render('lieu/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ajaxAction", name="lieu_ajaxAction", methods={"GET","POST"})
     * @param Request $request
     * @param LieuRepository $lieuRepository
     * @return Response
     */
    public function ajaxAction(Request $request, LieuRepository $lieuRepository){

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $id = $request->get('villeid');
        $liste = $lieuRepository->findBy(['ville'=>$id]);

        $json = json_encode($serializer->serialize($liste, 'json'));

        return new Response($json);
    }

    /**
     * @Route("/{id}", name="lieu_show", methods={"GET"})
     */
    public function show(Lieu $lieu): Response
    {
        return $this->render('lieu/show.html.twig', [
            'lieu' => $lieu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lieu_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lieu $lieu): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lieu_index');
        }

        return $this->render('lieu/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lieu_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lieu $lieu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lieu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lieu_index');
    }

}
