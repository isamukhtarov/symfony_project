<?php

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class AddNoindexToExternalLinks
 * @package Ria\Bundle\PostBundle\Pipe
 */
class AddNoindexToExternalLinks
{

    public function __construct(
        protected ParameterBagInterface $parameterBag,
        protected ContainerInterface $container
    )
    {
    }

    public function handle(string $content, Closure $next): string
    {
        $linksToReplace = $this->getLinksToReplace($content);
        $content        = str_replace(array_keys($linksToReplace), array_values($linksToReplace), $content);

        return $next($content);
    }

    protected function getLinksToReplace(string $content): array
    {
        $crawler = new Crawler($content);

        $links = $crawler->filter(sprintf("a:not([href^='%s'])", $this->getHostInfo()));

        $linksToReplace = $links->each(function (Crawler $link) {
            $linkHtml = $link->outerHtml();
            if (!str_contains($linkHtml, 'target="_blank"')) {
                return [$linkHtml => str_replace('>' . $link->html(), ' target="_blank">' . $link->html(), $linkHtml)];
            }
            return null;
        });
        $linksToReplace = array_filter($linksToReplace);

        foreach ($linksToReplace as $key => $params) {
            $linksToReplace[array_key_first($params)] = '<noindex>' . $params[array_key_first($params)] . '</noindex>';
            unset($linksToReplace[$key]);
        }

        return $linksToReplace;
    }

    protected function getHostInfo(): string
    {
        return $this->container->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost();
    }

}