<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;
use Yii;

/**
 * Class Export
 * @package Ria\News\Core\Query\Specifications\Post
 */
class Export extends BaseSpecification
{
    /**
     * @var string|null
     */
    private $export;

    /**
     * WithAuthor constructor.
     * @param string $export
     */
    public function __construct(string $export)
    {
        parent::__construct();

        $this->export = $export;
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
            Spec::innerJoin('exports', 'export'),
            Spec::eq('type', $this->export, 'export')
        );
    }

}