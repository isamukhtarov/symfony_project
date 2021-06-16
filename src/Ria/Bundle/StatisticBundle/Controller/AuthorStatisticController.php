<?php

declare(strict_types=1);

namespace Ria\Bundle\StatisticBundle\Controller;

use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Ria\Bundle\StatisticBundle\Component\ExcelExport;
use Ria\Bundle\StatisticBundle\Form\Grid\AuthorGrid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Yectep\PhpSpreadsheetBundle\Factory;

#[Route('statistic/authors', name: 'authors.')]
class AuthorStatisticController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translation,
        private RedisClient $redisClient
    ){}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(AuthorGrid $grid): Response
    {
        $this->redisClient->set('authorsStatisticData', serialize($grid->createView()->getItemList()));

        return $this->render('@RiaStatistic/statistic/index.html.twig', [
            'grid' => $grid->createView(),
            'dates' => $grid->getDates(),
            'path' => $this->generateUrl('authors.index'),
            'export_path' => $this->generateUrl('authors.export')
        ]);
    }

    #[Route('/excel/export', name: 'export')]
    public function makeExport(Factory $factory): Response
    {
        $authors = unserialize($this->redisClient->get('authorsStatisticData'));

        $authors = array_map(function ($author) {
            return [
                $this->translation->trans('statistic.Author') => $author['author_name'],
                $this->translation->trans('statistic.Post Views') => $author['post_views'] ?: 0,
                $this->translation->trans('statistic.Post Count') => $author['post_count']
            ];
        }, $authors);

        $excel = new ExcelExport();
        $excelOutput = $excel->export($factory, $authors);
        $filename = date('Y-m-d') . '_' . time() . '_exported';

        return new Response(
            $excelOutput,
            200,
            [
                'content-type'        =>  'text/x-xlsx; charset=UTF-8',
                'Content-Disposition' =>  "attachment; filename={$filename}.xlsx"
            ]
        );
    }
}