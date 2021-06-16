<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Filter\Filter;
use Happyr\DoctrineSpecification\Spec;

class WithExpert extends BaseSpecification
{
    /**
     * @var string
     */
    private string $language;

    /**
     * @var int|null
     */
    private ?int $id;

    /**
     * WithExpert constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct(($params['dqlAlias']) ?? null);

        $this->language = $params['language'];
        $this->id       = $params['id'] ?? null;
    }

    /**
     * @return Filter
     * |\Happyr\DoctrineSpecification\Logic\AndX
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
        return Spec::andX(
            Spec::innerJoin('expert', 'ex'),
            Spec::innerJoin('translations', 'ext', 'ex'),
            $this->id == null ?: Spec::eq('id', $this->id, 'ex'),
            Spec::eq('language', $this->language, 'ext'),
            Spec::addSelect(
                Spec::selectAs(Spec::field('first_name', 'ext'), 'expert_first_name'),
                Spec::selectAs(Spec::field('last_name', 'ext'), 'expert_last_name'),
                Spec::selectAs(Spec::field('position', 'ext'), 'expert_position'),
                Spec::selectAs(Spec::field('slug', 'ext'), 'expert_slug'),
                Spec::selectAs(Spec::field('photo', 'ex'), 'expert_thumb'),
            )
        );
    }
}