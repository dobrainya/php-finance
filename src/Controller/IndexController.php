<?php

namespace App\Controller;

use App\Entity\Reference;
use App\Repository\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class IndexController extends AbstractController
{
    public function __construct(private readonly ReferenceRepository $referenceRepository)
    {
    }

    #[Route('/refs', name: 'app_references')]
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

    #[Route('/test', name: 'app_references_tt')]
    public function test(): JsonResponse
    {
        $data = $this->referenceRepository->findAll();

        return $this->json($data);
    }
}
