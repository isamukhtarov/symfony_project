<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Category
 * @package Ria\News\Core\Query\Specifications\Post
 */
class Category extends BaseSpecification
{
    /**
     * @var array|string
     */
    private array|string $categoryId;

    /**
     * Limit constructor.
     * @param int|array $categoryId
     * @param null $dqlAlias
     */
    public function __construct(int|array $categoryId, $dqlAlias = null)
    {
        $this->categoryId = $categoryId;
        parent::__construct($dqlAlias);
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\In
     * \Happyr\DoctrineSpecification\Filter\Filter|
     * \Happyr\DoctrineSpecification\Filter\In|
     * \Happyr\DoctrineSpecification\Query\QueryModifier|null
     */
    public function getSpec()
    {
        return Spec::in('category', array_wrap($this->categoryId));
    }

}