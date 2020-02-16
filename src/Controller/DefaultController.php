<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 *
 * @Route("/")
 *
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = [
            'action' => 'index',
        ];

        return $this->json($response);
    }
}