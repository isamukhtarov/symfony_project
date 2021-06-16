<?php
declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Mobile_Detect;
use Ria\Bundle\VoteBundle\Command\AddVoteCommand;
use Ria\Bundle\VoteBundle\Entity\Log;
use Ria\Bundle\VoteBundle\Entity\Option;
use Ria\Bundle\VoteBundle\Entity\Vote;
use Ria\Bundle\VoteBundle\Repository\VoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AddVoteHandler
{
    private ?Request $request;
    private Mobile_Detect $mobileDetect;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private VoteRepository $voteRepository,
        RequestStack $requestStack
    ) {
        $this->mobileDetect = new Mobile_Detect();
        $this->request = $requestStack->getMasterRequest();
    }

    public function handle(AddVoteCommand $command)
    {
        $vote = $this->entityManager->find(Vote::class, $command->voteId);
        $option = $this->entityManager->find(Option::class, $command->optionId);

        if (!$this->checkIsUserVoted($command->voteId)) {
            $option->incrementScore();
        }

        $log = (new Log())
            ->setUserAgent($this->mobileDetect->getUserAgent())
            ->setIp($this->request->getClientIp());

        $option->addLog($log);
        $vote->addLog($log);

        $this->entityManager->persist($vote);
        $this->entityManager->persist($option);
        $this->entityManager->flush();
    }

    private function checkIsUserVoted(int $voteId): bool
    {
        $query = $this->entityManager
            ->getRepository(Log::class)
            ->createQueryBuilder('l')
            ->select("count(l.userAgent)")
            ->where("l.userAgent = :userAgent")
            ->andWhere("l.ip = :ip")
            ->andWhere('IDENTITY(l.vote) = :vote_id')
            ->setParameter('userAgent', $this->mobileDetect->getUserAgent())
            ->setParameter('ip', $this->request->getClientIp())
            ->setParameter('vote_id', $voteId);

        return $query->getQuery()->getSingleScalarResult() > 0;
    }

}