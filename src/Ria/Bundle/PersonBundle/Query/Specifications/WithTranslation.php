<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Query\Specifications;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Operand\Value;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class WithTranslation
 * @package Ria\Persons\Core\Query\Specifications
 */
class WithTranslation extends BaseSpecification
{
    /**
     * @var string|null
     */
    private $language;

    /**
     * @var string|null
     */
    private $orderBy;

    /**
     * @var string|null
     */
    private $filter = null;

    /**
     * WithTranslation constructor.
     * @param $params
     */
    public function __construct($params)
    {
        parent::__construct(isset($params['dqlAlias']) ? $params['dqlAlias'] : null);

        $this->language = $params['language'];
        empty($params['orderBy']) ?: $this->orderBy = $params['orderBy'];
        empty($params['filter']) ?: $this->filter = $params['filter'];
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Filter
     * |\Happyr\DoctrineSpecification\Logic\AndX
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
        return Spec::andX(
            Spec::innerJoin('translations', 'pt'),
            Spec::eq('language', $this->language, 'pt'),
            Spec::addSelect(
                Spec::selectAs(Spec::field('first_name', 'pt'), 'first_name'),
                Spec::selectAs(Spec::field('last_name', 'pt'), 'last_name'),
                Spec::selectAs(Spec::field('position', 'pt'), 'position'),
                Spec::selectAs(Spec::field('text', 'pt'), 'timeline'),
                Spec::selectAs(Spec::field('slug', 'pt'), 'slug'),
            ),
            $this->orderBy == null ?:
                Spec::orderBy($this->orderBy, 'DESC', 'pt'),
            $this->filter == null ?:
                Spec::neq(Spec::REGEXP(Spec::field('last_name', 'pt'), new Value('[[:<:]]' . $this->filter)), $this->filter, 'pt')
        );
    }
}