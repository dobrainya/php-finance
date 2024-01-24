<?php

namespace App\Controller;

use App\Entity\Amount;
use App\Repository\AmountRepository;
use App\Repository\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/amount', name: 'api_amount_')]
class AmountController extends AbstractController
{
    public function __construct(
        private readonly ReferenceRepository $referenceRepository,
        private readonly AmountRepository $amountRepository)
    {
    }

    #[Route('/incomes', name: 'incomes_', methods: ['get'])]
    public function incomes(): JsonResponse
    {
        return $this->json($this->getAmountsByCategory('inc'));
    }

    #[Route('/expenses', name: 'expenses_', methods: ['get'])]
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
                'type' => $amount->getType()->getCode(),
            ];
        }, $data);
    }

    #[Route('/create', name: 'create_', methods: ['post'])]
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $reference = $this->referenceRepository->findOneBy([
            'code' => $request->request->get('type'),
        ]);

        if (!$reference) {
            return $this->json(['success' => false], 400);
        }

        $amount = new Amount();
        $amount->setCreatedAt(new \DateTime());

        $name = $request->request->get('name');
        $amount->setName($name);

        $value = $request->request->get('amount');
        $amount->setAmount($value);
        $amount->setType($reference);

        $entityManager->persist($amount);
        $entityManager->flush();

        return $this->json([
            'id' => $amount->getId(),
            'name' => $amount->getName(),
            'amount' => $amount->getAmount(),
            'createdAt' => $amount->getCreatedAt(),
            'type' => $amount->getType()->getCode(),
        ]);
    }

    #[Route(
        '/update/{id}',
        name: 'update_',
        requirements: ['id' => '\d+'],
        methods: ['post'],
    )]
    public function update(Amount $amount, Request $request, EntityManagerInterface $entityManager)
    {
        $amount->setName($request->request->get('name'));
        $amount->setAmount($request->request->get('amount'));
        $entityManager->flush();

        return $this->json([
            'success' => true,
        ]);
    }
}
