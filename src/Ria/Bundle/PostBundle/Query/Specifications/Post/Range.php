<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Filter\Filter;
use Happyr\DoctrineSpecification\Spec;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

/**
 * Class Range
 * @package Ria\Bundle\PostBundle\Query\Specifications\Post
 */
class Range extends BaseSpecification
{

    public const RANGE_TODAY      = 'today';
    public const RANGE_YESTERDAY  = 'yesterday';
    public const RANGE_THIS_WEEK  = 'this_week';
    public const RANGE_THIS_MONTH = 'this_month';
    public const RANGE_PREV_WEEK  = 'last_week';
    public const RANGE_PREV_MONTH = 'last_month';

    #[Pure] public function __construct(
        protected string $range,
        $dqlAlias = null
    )
    {
        if (!in_array($this->range, self::getRanges())) {
            throw new InvalidArgumentException('Invalid range');
        }

        parent::__construct($dqlAlias);
    }

    public static function getRanges(): array
    {
        return [
            self::RANGE_TODAY,
            self::RANGE_YESTERDAY,
            self::RANGE_THIS_WEEK,
            self::RANGE_THIS_MONTH,
            self::RANGE_PREV_WEEK,
            self::RANGE_PREV_MONTH
        ];
    }

    /**
     * @return Filter
     */
    public function getSpec(): Filter
    {
        [$first, $second] = $this->getRangeFilters();

        return Spec::andX(
            Spec::gte('publishedAt', $first),
            Spec::lte('publishedAt', $second),
        );
    }

    protected function getRangeFilters(): array
    {
        switch ($this->range) {
            case self::RANGE_TODAY:
                return [
                    date('Y-m-d 00:00:00'),
                    date('Y-m-d 23:59:59')
                ];
            case self::RANGE_YESTERDAY:
                return [
                    date('Y-m-d 00:00:00', strtotime('-1 day', time())),
                    date('Y-m-d 23:59:59', strtotime('-1 day', time()))
                ];
            case self::RANGE_THIS_WEEK:
                return [
                    date('Y-m-d 00:00:00', strtotime('last monday midnight')),
                    date('Y-m-d 23:59:59')
                ];
            case self::RANGE_THIS_MONTH:
                return [
                    date('Y-m-01 00:00:00'),
                    date('Y-m-d 23:59:59')
                ];
            case self::RANGE_PREV_WEEK:
                $previousWeek = strtotime("-1 week +1 day");
                $startWeek    = strtotime("last sunday midnight", $previousWeek);
                $endWeek      = strtotime("next saturday", $startWeek);

                return [
                    date('Y-m-d 00:00:00', $startWeek),
                    date('Y-m-d 23:59:59', $endWeek)
                ];
            case self::RANGE_PREV_MONTH:
                return [
                    date('Y-m-d 00:00:00', strtotime('first day of previous month')),
                    date('Y-m-d 23:59:59', strtotime('last day of previous month'))
                ];
        }
    }

}