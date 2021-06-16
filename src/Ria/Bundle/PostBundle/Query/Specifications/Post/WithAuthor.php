<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;
use Yii;

/**
 * Class WithAuthor
 * @package Ria\News\Core\Query\Specifications\Post
 */
class WithAuthor extends BaseSpecification
{
    /**
     * @var string|null
     */
    private $language;

    /**
     * @var int
     */
    private $id;

    /**
     * WithAuthor constructor.
     * @param array|string $params
     */
    public function __construct($params)
    {
        parent::__construct(($params['dqlAlias']) ?? null);

        $this->language = $params['language'];
        $this->id       = $params['id'] ?? null;
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
            Spec::innerJoin('author', 'a'),
            Spec::innerJoin('translations', 'at', 'a'),
            Spec::leftJoin('photo', 'aph', 'a'),
            $this->id == null ?: Spec::eq('id', $this->id, 'a'),
            Spec::eq('language', $this->language, 'at'),
            Spec::addSelect(
                Spec::selectAs(Spec::field('firstName', 'at'), 'authorFirstName'),
                Spec::selectAs(Spec::field('lastName', 'at'), 'authorLastName'),
                Spec::selectAs(Spec::field('position', 'at'), 'authorPosition'),
                Spec::selectAs(Spec::field('slug', 'at'), 'authorSlug'),
                Spec::selectAs(Spec::field('filename', 'aph'), 'authorThumb'),
            )
        );
    }

}