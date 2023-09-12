<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RapidApiClient;
use App\Request\HistoricalQuotesRequest;
use Fig\Http\Message\RequestMethodInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    private RapidApiClient $rapiApiClient;

    public function __construct(RapidApiClient $rapidApiClient)
    {
        $this->rapidApiClient = $rapidApiClient;
    }

    #[Route('/report', methods: [RequestMethodInterface::METHOD_POST])]
    public function generateReport(HistoricalQuotesRequest $request): JsonResponse
    {
        try {
            $historicalData = $this->rapidApiClient->getHistoricalData($request->symbol, $request->startDate, $request->endDate);
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        //Dispatch event to send email


        return $this->json($historicalData);
    }
}
