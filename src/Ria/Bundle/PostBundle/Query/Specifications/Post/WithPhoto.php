<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class WithPhoto
 * @package Ria\News\Core\Query\Specifications\Post
 */
class WithPhoto extends BaseSpecification
{
    /**
     * @var string
     */
    private string $language;

    /**
     * WithCategory constructor.
     * @param string $language
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
            Spec::leftJoin('photo', 'ph'),
            Spec::addSelect(
                Spec::selectAs(Spec::field('filename', 'ph'), 'image'),
            ),
        );
    }

}