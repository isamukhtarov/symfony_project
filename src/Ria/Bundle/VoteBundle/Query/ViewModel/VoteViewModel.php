<?php

namespace Ria\Bundle\VoteBundle\Query\ViewModel;

use DateTime;
use Ria\Bundle\CoreBundle\Query\ViewModel;

/**
 * @property-read int $id
 * @property-read bool $showRecaptcha
 * @property-read string $title
 * @property-read string $type
 * @property-read DateTime $startDate
 * @property-read DateTime $endDate
 * @property-read string|null $photo
 * @property array $options
 * @property int $totalScore
 */
class VoteViewModel extends ViewModel
{

    public function getTotalScore(): int
    {
        if (isset($this->totalScore)) {
            return $this->totalScore;
        }

        $sum = 0;

        if (!isset($this->options)) {
            return $sum;
        }

        foreach ($this->options as $option) {
           $sum += $option['score'];
        }

        $this->totalScore = $sum;
        return $sum;
    }

    public function getOptionPercentage(array $option): int
    {
        $totalScore = $this->getTotalScore();
        return $totalScore > 0 ? (int)round($option['score'] / $totalScore * 100) : 0;
    }

}