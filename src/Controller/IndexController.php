<?php

namespace App\Controller;

use App\Entity\Amount;
use App\Entity\Reference;
use App\Repository\AmountRepository;
use App\Repository\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private ReferenceRepository $referenceRepository;
    private AmountRepository $amountRepository;

    /**
     * @param ReferenceRepository $referenceRepository
     * @param AmountRepository $amountRepository
     */
    public function __construct(ReferenceRepository $referenceRepository, AmountRepository $amountRepository)
    {
        $this->referenceRepository = $referenceRepository;
        $this->amountRepository = $amountRepository;
    }

    #[Route('/references', name: 'app_references')]
    public function references(): JsonResponse
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

    #[Route('/incomes', name: 'app_incomes')]
    public function incomes(): JsonResponse
    {
        return $this->json($this->getAmountsByCategory('inc'));
    }

    #[Route('/expenses', name: 'app_expenses')]
    public function expenses(): JsonResponse
    {
        return $this->json($this->getAmountsByCategory('exp'));
    }

    private function getAmountsByCategory(string $category): array
    {
        $reference = $this->referenceRepository->findOneBy([
            'code' => $category,
        ]);

        if (!$reference) {
            throw new \RuntimeException("Reference {$category} not found");
        }

        $data = $this->amountRepository->findBy([
            'type' => $reference,
        ]);

        return array_map(static function (Amount $amount) {
            return [
                'id' => $amount->getId(),
                'name' => $amount->getName(),
                'amount' => $amount->getAmount(),
                'createdAt' => $amount->getCreatedAt(),
            ];
        }, $data);
    }
}
