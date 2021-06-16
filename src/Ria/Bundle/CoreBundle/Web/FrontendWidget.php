<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Web;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Twig\Environment;

/**
 * Class FrontendWidget
 * @package Ria\Bundle\CoreBundle\Widgets
 *
 * @property-read string template
 * @property-read array|null filters
 * @property-read array|null method
 */
abstract class FrontendWidget
{
    protected string $alias;
    protected array $options = [];

    public function __construct(
        protected Environment $twig
    ) {}

    abstract public function run(): string;

    public function setOptions(array $options): static
    {
        $this->options  = $options;
        return $this;
    }

    public function setAlias(string $alias): static
    {
        $this->alias = $alias;
        return $this;
    }

    public function __get(string $name): mixed
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException("Parameter {$name} not found.");
        }
        return $this->options[$name];
    }

    #[Pure] public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function render(string $view, array $params = []): string
    {
        return $this->twig->render("@RiaWeb/{$this->alias}/{$view}", $params);
    }

}