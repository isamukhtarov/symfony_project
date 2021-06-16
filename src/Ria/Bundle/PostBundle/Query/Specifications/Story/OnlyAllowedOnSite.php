<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Story;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Filter\Comparison;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class OnlyAllowedOnSite
 * @package Ria\News\Core\Query\Specifications\Story
 */
class OnlyAllowedOnSite extends BaseSpecification
{

    /**
     * @return Comparison|
     * \Happyr\DoctrineSpecification\Filter\Filter|
     * \Happyr\DoctrineSpecification\Query\QueryModifier|null
     */
    public function getSpec()
    {
        return Spec::eq('showOnSite', true);
    }

}