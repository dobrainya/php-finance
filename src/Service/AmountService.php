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

    public function getAmountsByCategory(string $code): AmountListResponse
    {
        if (!$this->referenceRepository->existsByCode($code)) {
            throw new CategoryNotFoundException();
        }

        $data = array_map(
            fn (Amount $amount) => AmountItem::createFromEntity($amount),
            $this->amountRepository->findByCategoryCode($code)
        );

        return new AmountListResponse($data);
    }

    public function create(Request $request): AmountItemResponse
    {
        $category = $request->request->get('type');

        if (!$this->referenceRepository->existsByCode($category)) {
            throw new CategoryNotFoundException();
        }

        $amount = new Amount();
        $amount->setCreatedAt(new \DateTime());

        $name = $request->request->get('name');
        $amount->setName($name);

        $value = $request->request->get('amount');
        $amount->setAmount($value);
        $amount->setType($this->referenceRepository->findOneByCode($category));

        $this->entityManager->persist($amount);
        $this->entityManager->flush();

        return new AmountItemResponse(AmountItem::createFromEntity($amount));
    }
}
