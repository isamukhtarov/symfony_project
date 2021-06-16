<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Offset
 * @package Ria\News\Core\Query\Specifications\Post
 */
class Offset extends BaseSpecification
{
    /**
     * @var int
     */
    private $offset;

    /**
     * Limit constructor.
     * @param int $offset
     * @param null $dqlAlias
     */
    public function __construct(int $offset, $dqlAlias = null)
    {
        $this->offset = $offset;
        parent::__construct($dqlAlias);
    }

    /**
     * @return \Happyr\DoctrineSpecification\Query\Offset
     * |\Happyr\DoctrineSpecification\Query\Offset
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
        return Spec::offset($this->offset);
    }

}