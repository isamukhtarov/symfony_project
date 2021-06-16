<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;
use Ria\Bundle\CoreBundle\Query\SubSelect;

/**
 * Class HasAudio
 * @package Ria\News\Core\Query\Specifications\Post
 */
class HasAudio extends BaseSpecification
{
    /**
     * @var null
     */
    private $dqlAlias;

    /**
     * HasAudio constructor.
     * @param null $dqlAlias
     */
    public function __construct($dqlAlias = null)
    {
        parent::__construct($dqlAlias);
        $this->dqlAlias = $dqlAlias;
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Filter|\Happyr\DoctrineSpecification\Query\AddSelect|\Happyr\DoctrineSpecification\Query\QueryModifier|null
     */
    public function getSpec()
    {
        return Spec::addSelect(new SubSelect("(SELECT COUNT(ps.id) FROM Ria\Bundle\PostBundle\Entity\Post\Speech ps 
                                             WHERE ps.post = {$this->dqlAlias}.id) has_audio"));
    }

}