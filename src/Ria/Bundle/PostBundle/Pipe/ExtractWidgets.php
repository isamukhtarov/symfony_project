<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Entity\Widget\Widget;
use Ria\Bundle\PostBundle\Query\Hydrator\WidgetHydrator;
use Ria\Bundle\PostBundle\Query\ViewModel\WidgetViewModel;
use Tightenco\Collect\Support\Collection;

/**
 * Class ExtractWidgets
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ExtractWidgets
{

    private Collection $widgets;

    private bool $extracted = false;

    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function getWidgets(string $content): Collection
    {
        if (!$this->extracted) {
            $this->extractWidgets($content);
        }

        return $this->widgets;
    }

    protected function extractWidgets(string $content): void
    {
        preg_match_all('/{{widget-content-(?P<widget_id>[0-9]+)}}/', $content, $match);

        $qb = $this->entityManager->createQueryBuilder();

        $widgets = $qb->select('w')
            ->from(Widget::class, 'w')
            ->where($qb->expr()->in('w.id', ':widget_ids'))
            ->setParameters(['widget_ids' => $match['widget_id']])
            ->getQuery()
            ->getResult(WidgetHydrator::HYDRATION_MODE);

        $this->widgets   = new Collection($widgets);
        $this->extracted = true;
    }

    public function handle(string $content, Closure $next): string
    {
        $widgets = $this->getWidgets($content);

        foreach ($widgets as $widget) {
            /** @var WidgetViewModel $widget */
            $search = sprintf("{{widget-content-%s}}", $widget->id);

            $widgetContent = str_contains($widget->content, '<iframe')
                ? "<div class='iframe-container'>{$widget->content}</div>"
                : $widget->content;
            $content       = str_replace($search, $widgetContent, $content);
        }

        return $next($content);
    }

}