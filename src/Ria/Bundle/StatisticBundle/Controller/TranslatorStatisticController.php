<?php

declare(strict_types=1);

namespace Ria\Bundle\StatisticBundle\Controller;

use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Ria\Bundle\StatisticBundle\Component\ExcelExport;
use Ria\Bundle\StatisticBundle\Form\Grid\TranslatorGrid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Yectep\PhpSpreadsheetBundle\Factory;

#[Route('statistic/translators', name: 'translators.')]
class TranslatorStatisticController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translation,
        private RedisClient $redisClient
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(TranslatorGrid $grid): Response
    {
        $this->redisClient->set('translatorsStatisticData', serialize($grid->createView()->getItemList()));

        return $this->render('@RiaStatistic/statistic/index.html.twig', [
            'grid' => $grid->createView(),
            'dates' => $grid->getDates(),
            'path' => $this->generateUrl('translators.index'),
            'export_path' => $this->generateUrl('translators.export')
        ]);
    }

    #[Route('/excel/export', name: 'export')]
    public function makeExport(Factory $factory): Response
    {
        $translators = unserialize($this->redisClient->get('translatorsStatisticData'));

        $translators = array_map(function ($translator) {
            return [
                $this->translation->trans('statistic.Translator') => $translator['translator_name'],
                $this->translation->trans('statistic.Post Views') => $translator['post_views'] ?: 0,
                $this->translation->trans('statistic.Post Count') => $translator['post_count']
            ];
        }, $translators);

        $excel = new ExcelExport();
        $excelOutput = $excel->export($factory, $translators);
        $filename = date('Y-m-d') . '_' . time() . '_exported';

        return new Response(
            $excelOutput,
            200,
            [
                'content-type'        =>  'text/x-xlsx; charset=UTF-8',
                'Content-Disposition' => "attachment; filename={$filename}.xlsx"
            ]
        );
    }
}