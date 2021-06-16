<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;
use Ria\Bundle\CoreBundle\Query\IdentitySelect;

/**
 * Class WithCategory
 * @package Ria\News\Core\Query\Specifications\Post
 */
class WithCategory extends BaseSpecification
{
    /**
     * @var string
     */
    private $language;

    /**
     * WithCategory constructor.
     * @param string|null $language
     * @param null $dqlAlias
     */
    public function __construct(string $language, $dqlAlias = null)
    {
        parent::__construct($dqlAlias);
        $this->language = $language;
    }

    /**
     * @return \Happyr\DoctrineSpecification\Logic\AndX
     */
    public function getSpec()
    {
        return Spec::andX(
            Spec::addSelect(
                Spec::selectAs(new IdentitySelect('category'), 'category_id'),
                Spec::selectAs('language', 'language'),
            ),
        );
    }

}