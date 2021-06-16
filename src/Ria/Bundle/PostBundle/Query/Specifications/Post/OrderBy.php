<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class OrderBy
 * @package Ria\News\Core\Query\Specifications\Post
 */
class OrderBy extends BaseSpecification
{

    /**
     * @var string
     */
    private $field;

    /**
     * OrderBy constructor.
     * @param array $field
     * @param null $dqlAlias
     */
    public function __construct($field, $dqlAlias = null)
    {
        parent::__construct($dqlAlias);
        $this->field = $field;
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Filter|\Happyr\DoctrineSpecification\Query\OrderBy
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
        return Spec::orderBy($this->field, 'DESC');
    }

}