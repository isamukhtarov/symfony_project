<?php

declare(strict_types=1);

namespace Ria\Bundle\StatisticBundle\Component;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class Filters
{
    protected string $filter;
    protected ?string $fromDate;
    protected ?string $toDate;

    protected const  FILTER_SPEC_DATE = 'spec_date';
    protected const  FILTER_WEEK  = 'week';
    protected const  FILTER_MONTH = 'month';
    protected const  FILTER_3_MONTHS  = '3 months';
    protected const  FILTER_6_MONTHS  = '6 months';

    protected ?Request $request;
    protected TranslatorInterface $translation;

    public function __construct(TranslatorInterface $translation, RequestStack $requestStack){
        $this->translation = $translation;
        $this->request = $requestStack->getMasterRequest();
        $this->filter = !empty($this->request->get('filter')) ? $this->request->get('filter') : self::FILTER_MONTH;
        $this->fromDate = $this->request->get('from_date');
        $this->toDate = $this->request->get('to_date');
    }

    public function dateFilters(): array
    {
        return [
            self::FILTER_SPEC_DATE => $this->translation->trans('statistic.Spec Date'),
            self::FILTER_WEEK      => $this->translation->trans('statistic.Week'),
            self::FILTER_MONTH     => $this->translation->trans('statistic.Month'),
            self::FILTER_3_MONTHS  => $this->translation->trans('statistic.3 month'),
            self::FILTER_6_MONTHS  => $this->translation->trans('statistic.6 month'),
        ];
    }

    public function getAppropriatesParams(): array
    {
        switch ($this->filter) {
            case self::FILTER_MONTH:
            default:
                return ['date' => new DateTime('-1 month')];
            case self::FILTER_WEEK:
                return ['date' => new DateTime('-1 week')];
            case self::FILTER_3_MONTHS:
                return ['date' => new DateTime('-3 months')];
            case self::FILTER_6_MONTHS:
                return ['date' => new DateTime('-6 months')];
            case self::FILTER_SPEC_DATE:
                return [
                    'date_from' => $this->fromDate . '00:00:00',
                    'date_to' => $this->toDate . '23:59:59'
                ];
        }
    }

    public function getDates(): array
    {
        $dates = [];
        foreach ($this->dateFilters() as $dateKey => $label) {
            $dates[$dateKey] = $label;
        }
        return $dates;
    }

    protected function getAppropriateQuery(): string
    {
        if ($this->filter == self::FILTER_SPEC_DATE) {
            return "pv.createdAt BETWEEN :date_from AND :date_to";
        } else {
            return "pv.createdAt >= :date";
        }
    }

    public function getAppropriateQueryForPostCount(): string
    {
        if ($this->filter == self::FILTER_SPEC_DATE) {
            return "pl.publishedAt BETWEEN '{$this->fromDate} 00:00:00' AND '{$this->toDate} 23:59:59'";
        }else {
            return "pl.publishedAt >= :date";
        }
    }

}