<?php

namespace App\Controller;

use App\Entity\Reference;
use App\Repository\ReferenceRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class IndexController extends AbstractController
{
    public function __construct(private readonly ReferenceRepository $referenceRepository)
    {
    }

    /**
     * @OA\Response(response=200,description="Get references")
     */
    #[Route('/refs', name: 'app_references', methods: ['get'])]
    public function refs(): JsonResponse
    {
        $data = $this->referenceRepository->findAll();

        return $this->json(array_map(static function (Reference $reference) {
            return [
                'id' => $reference->getId(),
                'name' => $reference->getName(),
                'code' => $reference->getCode(),
            ];
        }, $data));
    }
}
