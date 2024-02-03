<?php

namespace App\Service;

use App\Entity\Amount;
use App\Exception\CategoryNotFoundException;
use App\Model\AmountItem;
use App\Model\AmountItemResponse;
use App\Model\AmountListResponse;
use App\Model\Request\AmountCreateRequest;
use App\Repository\AmountRepository;
use App\Repository\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;

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

    public function create(AmountCreateRequest $request): AmountItemResponse
    {
        if (!$this->referenceRepository->existsByCode($request->getType())) {
            throw new CategoryNotFoundException();
        }

        $amount = new Amount();
        $amount->setCreatedAt(new \DateTime());

        $amount->setName($request->getName());
        $amount->setAmount($request->getAmount());
        $amount->setType($this->referenceRepository->findOneByCode($request->getType()));

        $this->entityManager->persist($amount);
        $this->entityManager->flush();

        return new AmountItemResponse(AmountItem::createFromEntity($amount));
    }
}
