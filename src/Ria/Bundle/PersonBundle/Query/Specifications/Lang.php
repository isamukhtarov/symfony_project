<?php
declare(strict_types=1);

namespace Ria\Persons\Core\Query\Specifications;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Lang
 * @package Ria\Persons\Core\Query\Specifications
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