<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Ria\Bundle\CoreBundle\Component\HtmlBuilder;
use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Ria\Bundle\PostBundle\Entity\ExpertQuote\ExpertQuote;
use Ria\Bundle\PostBundle\Repository\ExpertQuoteRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class ExtractQuotes
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ExtractQuotes
{

    private string $language;

    public function __construct(
        protected ParameterBagInterface $parameterBag,
        protected ExpertQuoteRepository $repository,
        protected ImagePackage $imagePackage,
        string $language = null
    )
    {
        $this->language = $language ?? $this->parameterBag->get('app.locale');
    }

    public function handle(string $content, Closure $next) : string
    {
        preg_match_all('/{{expert-quote-(\d+)}}/i', $content, $matches);

        foreach ($matches[1] as $i => $quoteId) {
            $quote = $this->getQuote((int)$quoteId);

            if (empty($quote)) {
                $content = str_replace($matches[0][$i], '', $content);
                continue;
            }

            $content = str_replace($matches[0][$i], $this->getQuoteHtmlContent($quote), $content);
        }

        return $next($content);
    }

    protected function getQuote(int $id): ?ExpertQuote
    {
        return $this->repository->find($id);
    }

    protected function getQuoteHtmlContent(ExpertQuote $quote): string
    {
        $expertTranslation = $quote->getExpert()->getTranslation($this->language);

        $expertInfoHtml = HtmlBuilder::tag('div',
            HtmlBuilder::tag('div',
                    HtmlBuilder::tag('img', '',
                        ['src' => $this->imagePackage->getUrl($quote->getExpert()->getPhoto(), ['thumb' => 50])], false
                    ),
                ['class' => 'thumb']
            ) .
            HtmlBuilder::tag('div',
                HtmlBuilder::tag('span', $expertTranslation->getFirstName() . ' ' . $expertTranslation->getLastName(), ['class' => 'name']) .
                HtmlBuilder::tag('p', $expertTranslation->getPosition(), ['class' => 'he-is']), ['class' => 'info']
            )
            , [
                'class' => 'person flex'
            ]);

        return HtmlBuilder::tag('blockquote', HtmlBuilder::tag('p', $quote->getText()) . $expertInfoHtml, [
            'class' => 'person-quote'
        ]);
    }

}