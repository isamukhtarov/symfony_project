<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Region;


use Ria\Bundle\PostBundle\Command\Region\MoveDownRegionCommand;

class MoveDownRegionHandler
{
//    public function handle(MoveDownRegionCommand $command): void
//    {
//        /** @var Region $region */
//        $region = $this->entityManager->getRepository(Region::class)->find($command->getId());
//
//        $this->reorderOtherItems($region->getOrder(), $region->getOrder() + 1);
//
//        $region->setOrder($region->getOrder() + 1);
//
//        try {
//            $this->entityManager->persist($region);
//            $this->entityManager->flush();
//        } catch (DBALException $e) {
//            throw new CommandHandlerException($region, $e->getMessage());
//        }
//    }
//
//    /**
//     * @param int $oldPosition
//     * @param int $newPosition
//     */
//    protected function reorderOtherItems(int $oldPosition, int $newPosition)
//    {
//        $qb = $this->entityManager->createQueryBuilder();
//
//        // new position greater than old position,
//        // so all positions from old position + 1 up to and including new position should decrement
//        $qb
//            ->update(Region::class, 'c')
//            ->set('c.order', 'c.order - 1')
//            ->where('c.order BETWEEN :old AND :new')
//            ->setParameters(array_merge(
//                [':old' => $oldPosition + 1, ':new' => $newPosition]
//            ))
//            ->getQuery()
//            ->execute();
//    }
}