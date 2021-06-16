<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class WithCity
 * @package Ria\Bundle\PostBundle\Query\Specifications\Post
 */
class WithCity extends BaseSpecification
{
    /**
     * @var string|null
     */
    private $language;

    /**
     * @var int|null
     */
    private $id;

    /**
     * WithCity constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct(isset($params['dqlAlias']) ? $params['dqlAlias'] : null);

        $this->language = $params['language'];
        $this->id       = isset($params['id']) ? $params['id'] : null;
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
            Spec::innerJoin('city', 'ci'),
            Spec::innerJoin('translations', 'cit', 'ci'),
            $this->id == null ?: Spec::eq('id', $this->id, 'ci'),
            Spec::eq('language', $this->language, 'cit'),
            Spec::addSelect(
                Spec::selectAs(Spec::field('title', 'cit'), 'city_title'),
                Spec::selectAs(Spec::field('slug', 'cit'), 'city_slug'),
            )
        );
    }

}