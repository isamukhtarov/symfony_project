<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Component\DomCrawler\Crawler;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GeneratePersonLink
{
    protected ?string $content;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ParameterBagInterface $parameterBag,
    ){}

    public function handle(Post $post, Closure $next)
    {
        $this->content  = str_replace('&nbsp;', ' ', (string) $post->getContent());
        $languageForUrl = ($post->getLanguage() === $this->parameterBag->get('app.locale')) ? null : $post->getLanguage();
        $personsNames   = [];

        /** @var Person $person */
        foreach ($post->getPersons() as $person) {
            $translation    = $person->getTranslation($post->getLanguage());
            $personFullName = $translation->getFullName();
            $personsNames[] = $personFullName;

            if ($this->whetherPersonFoundInContent($personFullName)) {
                $this->applyLinkToPerson(
                    $personFullName,
                    $this->getPersonUrl($translation->getSlug(), $languageForUrl)
                );
            }
        }

        $this->removeUnnecessaryLinks($personsNames, $languageForUrl);
        $post->setContent($this->content);

        return $next($post);
    }

    protected function whetherPersonFoundInContent(string $personName): bool
    {
        return
            (mb_strpos($this->content, $personName, 0, 'utf-8') !== false) &&
            (mb_strpos($this->content, '>' . $personName . '</a', 0, 'utf-8') === false);
    }

    protected function applyLinkToPerson(string $personName, string $url): void
    {
        $this->content = preg_replace(
            '/' . $personName . '/',
            "<a href=\"{$url}\">{$personName}</a>",
            $this->content,
            1
        );
    }

    protected function getPersonUrl(string $slug, ?string $language): string
    {
        return $this->urlGenerator->generate('person_view', ['slug' => $slug, '_locale' => $language], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    protected function removeUnnecessaryLinks(array $boundPersonNames, ?string $language): void
    {
        $personLinkAsKeyword =  $this->urlGenerator->generate('persons', ['_locale' => $language], UrlGeneratorInterface::ABSOLUTE_URL);

        $crawler = new Crawler($this->content);
        $nodes = $crawler->filter('a[href^="' . $personLinkAsKeyword . '"]');

        if ($nodes->count() === 0) return;

        $linksRemoved = false;
        $nodes->each(function (Crawler $node) use ($boundPersonNames, &$linksRemoved) {
            if (!in_array($node->text(), $boundPersonNames)) {
                $element = $node->getNode(0);
                $element->parentNode->nodeValue = $node->text();
                $linksRemoved = true;
            }
        });

        if ($linksRemoved)
            $this->content = $crawler->html();
    }
}