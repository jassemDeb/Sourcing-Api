<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AuthService;


#[Route('/api', name: 'api_')]
class LoginController extends AbstractController
{

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[Route('/login', name: 'login', methods: 'post')]
    public function index(Request $request): JsonResponse
    {

        return $this->authService->Login($request);

    }
}
