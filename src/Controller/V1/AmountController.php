<?php

namespace App\Controller\V1;

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

#[Route('/api/v1/amount', name: 'api_v1_amount_')]
class AmountController extends AbstractController
{
    public function __construct(private readonly AmountService $amountService)
    {
    }

    #[Route('/', name: 'create_', methods: ['post'])]
    public function create(Request $request): JsonResponse
    {
        try {
            return $this->json($this->amountService->create($request));
        } catch (CategoryNotFoundException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @OA\Response(response=200, description="Get list of incomes", @Model(type=AmountListResponse::class))
     */
    #[Route(
        '/{category}',
        name: 'amounts_by_category_',
        requirements: ['category' => '(exp|inc)'],
        methods: ['get']
    )]
    public function incomes(Request $request): JsonResponse
    {
        try {
            return $this->json($this->amountService->getAmountsByCategory($request->attributes->get('category')));
        } catch (CategoryNotFoundException $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route(
        '/{id}',
        name: 'options_',
        requirements: ['id' => '\d+'],
        methods: ['options'],
    )]
    public function options(Amount $amount, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        return $this->json('');
    }

    #[Route(
        '/{id}',
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
