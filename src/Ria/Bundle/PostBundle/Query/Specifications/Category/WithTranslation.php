<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Category;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class WithTranslation extends BaseSpecification
{
    private ?string $language;

    /**
     * WithCategory constructor.
     * @param string|null $language
     * @param null $dqlAlias
     */
    public function __construct(?string $language = null, $dqlAlias = null)
    {
        parent::__construct($dqlAlias);
        $this->language = $language;
    }

    public function getSpec()
    {
        return Spec::andX(
            Spec::innerJoin('translations', 'ct'),
            Spec::eq('language', $this->language, 'ct'),
            Spec::addSelect(
                Spec::selectAs(Spec::field('title', 'ct'), 'title')
            ),
        );
    }

}