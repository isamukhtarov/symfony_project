<?php

namespace Ria\Bundle\CoreBundle\Console\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OldDataMigrationCommand extends Command
{

    protected ?EntityManagerInterface $oldEntityManager;

    public function __construct(
        protected ContainerInterface $container,
        protected EntityManagerInterface $entityManager,
        string $name = null
    )
    {
        $this->oldEntityManager = $this->container->get('doctrine.orm.old_entity_manager');;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $records = $this->entityManager->getConnection()->executeQuery("
                SELECT p.id as post_id,p.photo_id from posts p
                LEFT JOIN post_photo pp ON pp.post_id = p.id AND pp.photo_id = p.photo_id
                WHERE pp.post_id IS NULL AND p.photo_id IS NOT NULL
                ")
            ->fetchAllAssociative();

        foreach ($records as $i => $record) {
            $this->entityManager->getConnection()->insert('post_photo', array_merge($record, ['sort' => 0]));

            $this->renderStatus($i + 1, count($records));
        }

        return self::SUCCESS;
    }

    protected function renderStatus(int $done, int $total, int $size = 30)
    {
        // if we go over our bound, just ignore it
        if ($done > $total) {
            return;
        }

        if (empty($this->startTime)) {
            $this->startTime = time();
        }

        $now  = time();
        $perc = (double)($done / $total);
        $bar  = floor($perc * $size);

        $status_bar = "\r[";
        $status_bar .= str_repeat("=", (int)$bar);
        if ($bar < $size) {
            $status_bar .= ">";
            $status_bar .= str_repeat(" ", (int)($size - $bar));
        } else {
            $status_bar .= "=";
        }

        $disp = number_format($perc * 100, 0);

        $status_bar .= "] $disp%  $done/$total";

        $rate = !$done ? 0 : (($now - $this->startTime) / $done);
        $left = $total - $done;
        $eta  = round($rate * $left, 2);

        $elapsed = $now - $this->startTime;

        $status_bar .= " remaining: " . number_format($eta) . " sec.  elapsed: " . number_format($elapsed) . " sec.";

        echo "$status_bar  ";

        flush();

        // when done, send a newline
        if ($done == $total) {
            echo "\n";
        }
    }

    protected function createConformity(string $oldId, int $currentId, string $type)
    {
        $exists = (bool)$this->entityManager->getConnection()->executeQuery(
            "SELECT COUNT(id) FROM old_conformity WHERE old_id = :old AND type = :type",
            ['old' => $oldId, 'type' => $type]
        )->fetchFirstColumn()[0];

        if ($exists) {
            $this->entityManager
                ->getConnection()
                ->update('old_conformity', ['current_id' => $currentId], ['old_id' => $oldId, 'type' => $type]);
        } else {
            $this->entityManager->getConnection()
                ->insert('old_conformity', [
                    'old_id'     => $oldId,
                    'current_id' => $currentId,
                    'type'       => $type
                ]);
        }
    }

}