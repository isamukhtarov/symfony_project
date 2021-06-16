<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use InvalidArgumentException;
use Ria\Bundle\ConfigBundle\Service\ConfigPackage;
use Ria\Bundle\CoreBundle\Helper\StringHelper;
use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Ria\Bundle\YoutubeBundle\Repository\YoutubeRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class YoutubeVideosWidget extends FrontendWidget
{

    const LIVE_TYPE_YOUTUBE  = 'youtube';
    const LIVE_TYPE_FACEBOOK = 'facebook';

    public function __construct(
        protected YoutubeRepository $youtubeRepository,
        protected ParameterBagInterface $parameterBag,
        protected ConfigPackage $configPackage,
        Environment $twig
    )
    {
        parent::__construct($twig);
    }

    public function run(): string
    {
        $url = $this->configPackage->get('live_video');

        if (empty($url)) {
            return $this->renderVideosSlider();
        }

        try {
            $parsed = parse_url($url);
            if (empty($parsed['host'])) {
                return $this->renderVideosSlider();
            }

            $type = $this->getLiveType($parsed['host']);

            if ($type == self::LIVE_TYPE_YOUTUBE) {
                return $this->render('live_youtube.html.twig', [
                    'youtubeId' => StringHelper::getYouTubeHash($url),
                ]);
            } else {
                return $this->render('live_facebook.html.twig', [
                    'url' => $url,
                ]);
            }
        } catch (InvalidArgumentException $e) {
            return '';
        }
    }

    private function getLiveType(string $host): string
    {
        switch ($host) {
            case 'youtu.be':
            case 'www.youtube.com':
                return self::LIVE_TYPE_YOUTUBE;
            case 'www.facebook.com':
            case 'facebook.com':
            case 'www.fb.com':
            case 'fb.com':
                return self::LIVE_TYPE_FACEBOOK;
            default:
                throw new InvalidArgumentException('Invalid video host: ' . $host);
        }
    }

    private function renderVideosSlider(): string
    {
        if ($this->parameterBag->get('app.locale') != 'ge') {
            return '';
        }
        $videos = $this->youtubeRepository->createQueryBuilder('y')
            ->select(['y.youtubeId'])
            ->orderBy('y.id', 'desc')
            ->setMaxResults(3)
            ->getQuery()
            ->execute();
        if (empty($videos)) {
            return '';
        }
        return $this->render('index.html.twig', compact('videos'));
    }

}