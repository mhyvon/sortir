<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccueilController extends Controller
{
    /**
     *@Route("accueil", name ="accueil")
     */
    public function accueil(){

        return $this->render("twig/accueil.html.twig");
    }







}