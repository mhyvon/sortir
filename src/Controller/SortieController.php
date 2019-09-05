<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\ResearchType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/", name="sortie_index", methods={"GET","POST"})
     */
    public function index(SortieRepository $sortieRepository, Request $request, EntityManagerInterface $em ): Response

    {
        $recherche=$this->createForm(ResearchType::class);
        $recherche->handleRequest($request);

        //$this->getUser()->addRole('ROLE_ADMIN');


        if (!$this->getUser()->getActif()) {
            return $this->redirectToRoute('participant_logout');
        }



        if ($recherche->isSubmitted()&&$recherche->isValid()) {

            $mot = $recherche->get('motR')->getData();
            $site= $recherche->get('siteR')->getData();
            $dateD= $recherche->get('dateD')->getData();
            $dateF= $recherche->get('dateF')->getData();
            $orga= $recherche->get('orga')->getData();
            $inscr= $recherche->get('inscr')->getData();
            $nonInscr= $recherche->get('noninscr')->getData();
            $passe= $recherche->get('passe')->getData();

            $connecte = $this->getUser();

            $repo = $this->getDoctrine()->getRepository(Etat::class);
            $etat = $repo->findOneBy(['libelle'=>'Passée']);

            $maListe= $em->getRepository(Sortie::class)->rechercheSortie($mot, $site, $dateD, $dateF, $orga, $inscr, $nonInscr, $passe, $connecte, $etat);

            return $this->render('sortie/index.html.twig', [
                'sorties'=>$maListe,
                'form'=>$recherche->createView(),
            ]);
        }

        $liste = $sortieRepository->findAll();
        foreach ($liste as $sortie) {
            $this->maj($sortie);
        };

        return $this->render('sortie/index.html.twig', [
            'sorties' => $liste,
            'form'=>$recherche->createView(),
        ]);
    }

    /**
     * @Route("/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request, EtatRepository $repository): Response
    {
        $sortie = new Sortie();
        $sortie->setSite($this->getUser()->getSite());
        $sortie->setOrganisateur($this->getUser());

        $etat = $repository->findOneBy(['libelle'=>'Créée']);
        $sortie->setEtat($etat);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form['urlPhoto']->getData();


            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $sortie->setUrlPhoto($newFilename);
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Votre sortie a bien été créée !');
            return $this->redirectToRoute('sortie_index');
        }

        $lieu = new Lieu();
        $formlieu = $this->createForm(LieuType::class, $lieu);
        $formlieu->handleRequest($request);

        if($formlieu->isSubmitted()&&$formlieu->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->render('sortie/new.html.twig', [
                'sortie' => $sortie,
                'form' => $form->createView(),
                'formlieu'=>$formlieu->createView()
            ]);
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
            'formlieu'=>$formlieu->createView()
        ]);
    }

    /**
     * @param Sortie $sortie
     * @Route("/{id}/cancel", name="sortie_cancel", methods={"GET","POST"})
     * @return RedirectResponse
     */
    public function cancel(Sortie $sortie, EtatRepository $repo, EntityManagerInterface $em){

        $etat = $repo->findOneBy(['libelle'=>'Annulée']);

        $repo = $this->getDoctrine()->getRepository(Etat::class);

        $passe = $repo->findOneBy(['libelle'=>'Passée']);
        $enCours = $repo->findOneBy(['libelle'=>'Activité en cours']);
        $annulee = $repo->findOneBy(['libelle'=>'Annulée']);
        $perime = $repo->findOneBy(['libelle'=>'périmé']);

        if ($sortie->getEtat()!=$passe&&$sortie->getEtat()!=$enCours&&$sortie->getEtat()!=$annulee&&$sortie->getEtat()!=$perime) {

            if ($sortie->getOrganisateur()->getId() == $this->getUser()->getId()) {
                $sortie->setEtat($etat);
                $em->persist($sortie);
                $em->flush();
            }
        }

        return $this->redirectToRoute('sortie_index');
    }

    /**
     * @Route("/{id}/register", name="sortie_register", methods={"GET","POST"})
     * @param Sortie $sortie
     * @return RedirectResponse
     */
    public function addParticipant(Sortie $sortie){

        $repo = $this->getDoctrine()->getRepository(Etat::class);

        $passe = $repo->findOneBy(['libelle'=>'Passée']);
        $enCours = $repo->findOneBy(['libelle'=>'Activité en cours']);
        $annulee = $repo->findOneBy(['libelle'=>'Annulée']);
        $perime = $repo->findOneBy(['libelle'=>'périmé']);

        if ($sortie->getEtat()!=$passe&&$sortie->getEtat()!=$enCours&&$sortie->getEtat()!=$annulee&&$sortie->getEtat()!=$perime) {
            $sortie->addInscription($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($sortie);
            $em->flush();
        }

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

        $repo = $this->getDoctrine()->getRepository(Etat::class);

        $passe = $repo->findOneBy(['libelle'=>'Passée']);
        $enCours = $repo->findOneBy(['libelle'=>'Activité en cours']);
        $annulee = $repo->findOneBy(['libelle'=>'Annulée']);
        $perime = $repo->findOneBy(['libelle'=>'périmé']);

        if ($sortie->getEtat()!=$passe&&$sortie->getEtat()!=$enCours&&$sortie->getEtat()!=$annulee&&$sortie->getEtat()!=$perime) {
            $sortie->removeInscription($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($sortie);
            $em->flush();
        }

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
            /** @var UploadedFile $imageFile */
            $imageFile = $form['urlPhoto']->getData();


            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $sortie->setUrlPhoto($newFilename);
            }



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
        $dateDebut = new \DateTime($sortie->getDebut()->format('Y-m-d H:i:s'));


        $dateFin->add(new \DateInterval('PT' . $sortie->getDuree() . 'M'));

        if ($now>$sortie->getDebut()&&$now<$dateFin) {
            $etat = $repo->findOneBy(['libelle'=>'Activité en cours']);
            $sortie->setEtat($etat);
        }
        if ($now>$dateFin&&$sortie->getEtat()->getLibelle()!='Annulée'){
            $etat = $repo->findOneBy(['libelle'=>'Passée']);
            $sortie->setEtat($etat);
        }
        if ($now<$dateDebut&&$sortie->getEtat()->getLibelle()!='Annulée'){
            $etat = $repo->findOneBy(['libelle'=>'Créée']);
            $sortie->setEtat($etat);
        }



        $interval = new \DateInterval('P30D');
        $dateMoinsUnMois=$now->sub($interval);

        if ($dateMoinsUnMois>$dateFin&&$sortie->getEtat()->getLibelle()!='Annulée'){
            $etat = $repo->findOneBy(['libelle'=>'périmé']);
            $sortie->setEtat($etat);
        }

        $em->persist($sortie);
        $em->flush();
    }



    /**
     * @Route("/fb/blabla", name="sortie_facebook", methods={"GET", "POST"})
     */
    public function onBouge(){

        return $this->render('gif/facebook.html.twig');

    }

    /**
     * @Route("/twitter/blabla", name="sortie_twitter", methods={"GET", "POST"})
     */
    public function onBouge2(){

        return $this->render('gif/twitter.html.twig');

    }

    /**
     * @Route("/twitter/blablalalalalalala", name="sortie_linkedin", methods={"GET", "POST"})
     */
    public function onBouge3(){

        return $this->render('gif/linkedin.html.twig');

    }

}
