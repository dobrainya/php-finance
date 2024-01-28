<?php

namespace App\Controller\V1;

use App\Entity\Amount;
use App\Model\AmountItemResponse;
use App\Model\AmountListResponse;
use App\Model\ErrorResponse;
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

    /**
     * @OA\Response(
     *     response=200,
     *     description="Create new amount",
     *
     *     @Model(type=AmountItemResponse::class)
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Category not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route('/', name: 'create_', methods: ['post'])]
    public function create(Request $request): JsonResponse
    {
        return $this->json($this->amountService->create($request));
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Get list of amounts by category",
     *
     *     @Model(type=AmountListResponse::class)
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Category not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(
        '/{category}',
        name: 'amounts_by_category_',
        requirements: ['category' => '(exp|inc|test)'],
        methods: ['get']
    )]
    public function incomes(Request $request): JsonResponse
    {
        return $this->json($this->amountService->getAmountsByCategory($request->attributes->get('category')));
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

    /**
     * @OA\Response(
     *     response=200,
     *     description="Update amount by id",
     * )
     */
    #[Route(
        '/{id}',
        name: 'update_',
        requirements: ['id' => '\d+'],
        methods: ['post'],
    )]
    public function update(Amount $amount, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // todo: перенести логику в AmountService
        $amount->setName($request->request->get('name'));
        $amount->setAmount($request->request->get('amount'));
        $entityManager->flush();

        return $this->json([
            'success' => true,
        ]);
    }
}
