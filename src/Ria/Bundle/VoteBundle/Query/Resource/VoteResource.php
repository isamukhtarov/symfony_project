<?php
declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Query\Resource;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\CoreBundle\Query\Resource;
use Ria\Bundle\VoteBundle\Query\ViewModel\VoteViewModel;
use Ria\Bundle\VoteBundle\Repository\VoteRepository;
use Tightenco\Collect\Support\Collection;

class VoteResource extends Resource
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private VoteRepository $voteRepository,
    ) {}

    public function toArray($vote): array
    {
        /** @var VoteViewModel $vote */
        $options = $this->getOptions($vote);

        return array_replace($vote->toArray(), [
//            'page_url'      => Url::toRoute(['vote/index']),
            'id' => $vote->id,
            'showRecaptcha' => $vote->showRecaptcha,
            'title' => $vote->title,
            'start_date'    => $vote->startDate->format('j F, Y'),
            'end_date_long' => $vote->endDate->format('j F, Y'),
            'total_count'   => $options->sum('score'),
            'options'       => $options
        ]);
    }

    private function getOptions(VoteViewModel $vote): Collection
    {
        $options = $this->voteRepository->getVoteOptions($vote->id);

        $optionsCollection = (new Collection($options));

        return $optionsCollection->transform(function ($option) use ($optionsCollection) {
            $scoreSum = $optionsCollection->sum('score');

            return array_replace($option, [
                'percentage' => $scoreSum > 0 ? (int)round($option['score'] / $scoreSum * 100) : 0
            ]);
        });
    }

}