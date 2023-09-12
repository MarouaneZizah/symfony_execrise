<?php

namespace App\Controller;

use App\Service\NasdaqClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingsController extends AbstractController
{
    private NasdaqClient $nasdaqClient;

    public function __construct(NasdaqClient $nasdaqClient)
    {
        $this->nasdaqClient = $nasdaqClient;
    }

    #[Route('/companies')]
    public function getListings(): Response
    {
        $companies = $this->nasdaqClient->getCachedCompanies();

        return new JsonResponse($companies);
    }
}