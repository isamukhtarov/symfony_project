<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class MostRead
 * @package Ria\News\Core\Query\Specifications\Post
 */
class MostRead extends BaseSpecification
{

    /**
     * @var int
     */
    private $days;

    /**
     * OrderBy constructor.
     * @param int $days
     * @param null $dqlAlias
     */
    public function __construct(int $days, $dqlAlias = null)
    {
        parent::__construct($dqlAlias);
        $this->days = $days;
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Filter|\Happyr\DoctrineSpecification\Query\OrderBy
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
        return Spec::andX(
            Spec::gt('publishedAt', new \DateTime("-{$this->days} days")),
            Spec::orderBy('views', 'DESC')
        );
    }

}