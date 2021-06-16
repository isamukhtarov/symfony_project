<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Story;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class WithTranslation
 * @package Ria\News\Core\Query\Specifications\Story
 */
class WithTranslation extends BaseSpecification
{
    /**
     * @var string|null
     */
    private ?string $language;

    /**
     * WithTranslation constructor.
     * @param string|null $language
     * @param null $dqlAlias
     */
    public function __construct(string $language, $dqlAlias = null)
    {
        parent::__construct($dqlAlias);
        $this->language = $language;
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
            Spec::innerJoin('translations', 'ct'),
            Spec::eq('language', $this->language, 'ct'),
            Spec::addSelect(
                Spec::selectAs(Spec::field('title', 'ct'), 'title'),
                Spec::selectAs(Spec::field('slug', 'ct'), 'slug')
            )
        );
    }

}