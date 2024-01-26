<?php

namespace App\Controller;

use App\Model\ReferenceListResponse;
use App\Service\ReferenceService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class IndexController extends AbstractController
{
    public function __construct(private readonly ReferenceService $referenceService)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Get references",
     *
     *     @Model(type=ReferenceListResponse::class)
     * )
     */
    #[Route('/refs', name: 'app_references', methods: ['get'])]
    public function refs(): JsonResponse
    {
        return $this->json($this->referenceService->getReferences());
    }
}
