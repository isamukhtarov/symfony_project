<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post\Traits;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait Votes
{
    /**
     * @ORM\ManyToMany(targetEntity="Ria\Bundle\VoteBundle\Entity\Vote", inversedBy="posts")
     * @ORM\JoinTable(name="post_vote")
     */
    private Collection $votes;

    public function getVotes(): Collection
    {
        return $this->votes;
    }

}