<?php

namespace App\Service;

use App\Entity\Amount;
use App\Exception\CategoryNotFoundException;
use App\Model\AmountItem;
use App\Model\AmountItemResponse;
use App\Model\AmountListResponse;
use App\Repository\AmountRepository;
use App\Repository\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AmountService
{
    public function __construct(
        private readonly AmountRepository $amountRepository,
        private readonly ReferenceRepository $referenceRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getAmountsByCategory(string $category): AmountListResponse
    {
        $reference = $this->referenceRepository->findOneBy([
            'code' => $category,
        ]);

        if (!$reference) {
            throw new CategoryNotFoundException();
        }

        $data = $this->amountRepository->findBy(['type' => $reference]);

        $data = array_map(
            fn (Amount $amount) => AmountItem::createFromEntity($amount),
            $data
        );

        return new AmountListResponse($data);
    }

    public function create(Request $request): AmountItemResponse
    {
        $category = $request->request->get('type');

        $reference = $this->referenceRepository->findOneBy([
            'code' => $category,
        ]);

        if (!$reference) {
            throw new CategoryNotFoundException();
        }

        $amount = new Amount();
        $amount->setCreatedAt(new \DateTime());

        $name = $request->request->get('name');
        $amount->setName($name);

        $value = $request->request->get('amount');
        $amount->setAmount($value);
        $amount->setType($reference);

        $this->entityManager->persist($amount);
        $this->entityManager->flush();

        return new AmountItemResponse(AmountItem::createFromEntity($amount));
    }
}
