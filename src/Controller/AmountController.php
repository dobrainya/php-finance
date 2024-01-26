<?php

namespace App\Controller;

use App\Entity\Amount;
use App\Exception\CategoryNotFoundException;
use App\Model\AmountListResponse;
use App\Service\AmountService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amount', name: 'api_amount_')]
class AmountController extends AbstractController
{
    public function __construct(private readonly AmountService $amountService)
    {
    }

    /**
     * @OA\Response(response=200, description="Get list of incomes", @Model(type=AmountListResponse::class))
     */
    #[Route('/incomes', name: 'incomes_', methods: ['get'])]
    public function incomes(): JsonResponse
    {
        try {
            return $this->json($this->getAmountsByCategory('inc'));
        } catch (CategoryNotFoundException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @OA\Response(response=200, description="Get list of expenses", @Model(type=AmountListResponse::class))
     */
    #[Route('/expenses', name: 'expenses_', methods: ['get'])]
    public function expenses(): JsonResponse
    {
        try {
            return $this->json($this->getAmountsByCategory('exp'));
        } catch (CategoryNotFoundException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], $e->getCode());
        }
    }

    private function getAmountsByCategory(string $category): AmountListResponse
    {
        return $this->amountService->getAmountsByCategory($category);
    }

    #[Route('/create', name: 'create_', methods: ['post'])]
    public function create(Request $request): JsonResponse
    {
        try {
            return $this->json($this->amountService->create($request));
        } catch (CategoryNotFoundException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route(
        '/update/{id}',
        name: 'update_',
        requirements: ['id' => '\d+'],
        methods: ['post'],
    )]
    public function update(Amount $amount, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $amount->setName($request->request->get('name'));
        $amount->setAmount($request->request->get('amount'));
        $entityManager->flush();

        return $this->json([
            'success' => true,
        ]);
    }
}
