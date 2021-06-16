<?php
declare(strict_types=1);

namespace Ria\Bundle\DataGridBundle\Twig;

use JetBrains\PhpStorm\ArrayShape;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalsTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        protected array $gridParameterList,
        protected array $paginatorParameterList,
    ) {}

    #[ArrayShape(["data_grid" => "array"])]
    public function getGlobals(): array
    {
        return [
            "data_grid" => [
                'grid'      => $this->gridParameterList,
                'paginator' => $this->paginatorParameterList
            ]
        ];
    }

    public function getName(): string
    {
        return "data_grid_globals_extension";
    }
}
