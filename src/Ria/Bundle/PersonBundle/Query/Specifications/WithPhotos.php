<?php
declare(strict_types=1);

namespace Ria\Persons\Core\Query\Specifications;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class WithPhotos
 * @package Ria\Persons\Core\Query\Specifications
 */
class WithPhotos extends BaseSpecification
{

    /**
     * WithTranslation constructor.
     * @param string|null $language
     * @param null $dqlAlias
     */
    public function __construct($dqlAlias)
    {
        parent::__construct($dqlAlias);
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Filter
     * |\Happyr\DoctrineSpecification\Logic\AndX
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
//        return Spec::andX(
//            Spec::innerJoin('photos', 'ps'),
//            Spec::addSelect(
//                Spec::selectAs(Spec::field('photo_id', 'ps'), 'photo_id'),
//            )
//        );
    }

}