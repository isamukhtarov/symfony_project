<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class WithCategory
 * @package Ria\News\Core\Query\Specifications\Post
 */
class WithCities extends BaseSpecification
{

    /**
     * @var array
     */
    private $ids;

    /**
     * Limit constructor.
     * @param array $ids
     * @param null $dqlAlias
     */
    public function __construct(array $ids, $dqlAlias = null)
    {
        $this->ids = $ids;
        parent::__construct($dqlAlias);
    }

    public function getSpec()
    {
        return Spec::in('city', array_wrap($this->ids));
    }


//    /**
//     * @var string|null
//     */
//    private $ids;
//
//    /**
//     * WithCategory constructor.
//     * @param string|null $language
//     * @param null $dqlAlias
//     */
//    public function __construct(?string $language = null, $dqlAlias = null)
//    {
//        parent::__construct($dqlAlias);
//        $this->language = $language ?: \Yii::$app->language;
//    }
//
//    /**
//     * @return \Happyr\DoctrineSpecification\Filter\Filter
//     * |\Happyr\DoctrineSpecification\Logic\AndX
//     * |\Happyr\DoctrineSpecification\Query\QueryModifier
//     * |null
//     */
//    public function getSpec()
//    {
//        return Spec::andX(
//            Spec::innerJoin('city', 'ci'),
//            Spec::innerJoin('translations', 'cit', 'ci'),
//            Spec::eq('language', $this->language, 'cit'),
//            Spec::addSelect(
//                Spec::selectAs(Spec::field('title', 'cit'), 'city_title'),
//            )
//        );
//    }

}