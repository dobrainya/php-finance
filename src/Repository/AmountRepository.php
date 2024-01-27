<?php

namespace App\Repository;

use App\Entity\Amount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Amount>
 *
 * @method Amount|null find($id, $lockMode = null, $lockVersion = null)
 * @method Amount|null findOneBy(array $criteria, array $orderBy = null)
 * @method Amount[]    findAll()
 * @method Amount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AmountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Amount::class);
    }

    /**
     * @return array<Amount>
     */
    public function findByCategoryCode(string $categoryCode): array
    {
        $sql = <<<SQL
SELECT a.id
FROM amount a
    JOIN reference r ON r.id = a.type_id
WHERE r.code = :code
SQL;

        $ids = $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'code' => $categoryCode,
        ])->fetchFirstColumn();

        if ([] === $ids) {
            return [];
        }

        return $this->findBy(['id' => $ids]);
    }
}
