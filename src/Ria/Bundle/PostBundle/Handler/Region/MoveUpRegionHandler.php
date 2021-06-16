<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Region;


use Doctrine\DBAL\DBALException;
use Ria\Bundle\PostBundle\Command\Region\MoveUpRegionCommand;
use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;

class MoveUpRegionHandler
{
//    public function __construct(
//        private RegionRepository $regionRepository,
//    ) {}

//    public function handle(MoveUpRegionCommand $command): void
//    {
//        $region = $this->regionRepository->find($command->getId());
//
//        $newPosition = ($region->getOrder()) > 1 ? $region->getOrder() - 1 : 1;
//
//        $this->reorderOtherItems($region->getOrder(), $newPosition);
//
//        $region->setSort($newPosition);
//
//    }

//    /**
//     * @param int $oldPosition
//     * @param int $newPosition
//     */
//    protected function reorderOtherItems(int $oldPosition, int $newPosition)
//    {
//        $qb = $this->entityManager->createQueryBuilder();
//
//        // new position smaller than or equal to old position,
//        // so all positions from new position up to and including old position - 1 should increment
//        $qb
//            ->update(Region::class, 'c')
//            ->set('c.order', 'c.order + 1')
//            ->where('c.order BETWEEN :new AND :old')
//            ->setParameters(array_merge([
//                ':old' => $oldPosition + 1, ':new' => $newPosition]
//            ))
//            ->getQuery()
//            ->execute();
//    }
}