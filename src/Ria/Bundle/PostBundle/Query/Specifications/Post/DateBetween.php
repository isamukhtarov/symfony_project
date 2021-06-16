<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Filter\Filter;
use Happyr\DoctrineSpecification\Spec;
use JetBrains\PhpStorm\Pure;

/**
 * Class DateBetween
 * @package Ria\Bundle\PostBundle\Query\Specifications\Post
 */
class DateBetween extends BaseSpecification
{

    public array $rangeAsArray = [];

    /**
     * Limit constructor.
     * @param array $rangeAsArray
     * @param null $dqlAlias
     */
    #[Pure] public function __construct(array $rangeAsArray, $dqlAlias = null)
    {
        $this->rangeAsArray = $rangeAsArray;
        parent::__construct($dqlAlias);
    }

    /**
     * @return Filter
     */
    public function getSpec(): Filter
    {
        return Spec::andX(
            Spec::gte('publishedAt', $this->rangeAsArray[0]),
            Spec::lte('publishedAt', $this->rangeAsArray[1]),
        );
    }

}