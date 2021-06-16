<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;
use Ria\News\Core\Models\Speech\Speech;
use Yii;

/**
 * Class WithSpeech
 * @package Ria\News\Core\Query\Specifications\Post
 */
class WithSpeech extends BaseSpecification
{
    /**
     * @var string|null
     */
    private $language;

    /**
     * @var int|null
     */
    private $id;

    /**
     * WithCity constructor.
     * @param $params
     */
    public function __construct($params)
    {
        parent::__construct($params['dqlAlias'] ?? null);

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
            Spec::innerJoin('speech', 'ps'),
            Spec::addSelect(
                Spec::selectAs(Spec::field('filename', 'ps'), 'speech_filename'),
                Spec::selectAs(Spec::field('duration', 'ps'), 'speech_duration'),
                Spec::selectAs(Spec::field('size', 'ps'), 'speech_size'),
            )
        );
    }

}