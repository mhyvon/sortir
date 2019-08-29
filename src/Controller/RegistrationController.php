<?php


namespace App\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class RegistrationController extends BaseController
{

    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenStorage;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($eventDispatcher, $formFactory,$userManager, $tokenStorage);
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function registerAction(Request $request)
    {
        $response = parent::registerAction($request);

        return $response;
    }

}