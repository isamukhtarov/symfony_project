<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Lang
 * @package Ria\News\Core\Query\Specifications\Post
 */
class Lang extends BaseSpecification
{
    /**
     * @var string
     */
    private $language;

    /**
     * Limit constructor.
     * @param string $language
     * @param null $dqlAlias
     */
    public function __construct(string $language, $dqlAlias = null)
    {
        $this->language = $language;
        parent::__construct($dqlAlias);
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Comparison
     * |\Happyr\DoctrineSpecification\Filter\Filter
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
        return Spec::eq('language', $this->language);
    }

}