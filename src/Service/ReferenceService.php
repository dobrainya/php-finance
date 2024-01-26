<?php

namespace App\Service;

use App\Entity\Reference;
use App\Model\ReferenceItem;
use App\Model\ReferenceListResponse;
use App\Repository\ReferenceRepository;

class ReferenceService
{
    public function __construct(
        private readonly ReferenceRepository $referenceRepository,
    ) {
    }

    public function getReferences(): ReferenceListResponse
    {
        $data = $this->referenceRepository->findAll();

        $data = array_map(
            fn (Reference $reference) => ReferenceItem::createFromEntity($reference),
            $data
        );

        return new ReferenceListResponse($data);
    }
}
