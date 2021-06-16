<?php

declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Twig;


use Psr\Container\ContainerInterface;
use Ria\Bundle\AdminBundle\Voter\AccessVoter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class MenuExtension
 * @package Ria\Bundle\AdminBundle\Twig
 */
class MenuExtension extends AbstractExtension
{
    public function __construct(
        private TranslatorInterface $translator,
        private ContainerInterface $container,
        private Security $security,
        private AccessVoter $voter
    ){}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_menu', [$this, 'renderMenu'], ['is_safe' => ['all']])
        ];
    }

    public function renderMenu(): string
    {
        $menuTree = Yaml::parseFile(__DIR__ . '/../Resources/config/menu.yml');
        $masterRequest = $this->container->get('request_stack')->getMasterRequest();
        return $this->getItems($menuTree, $masterRequest->attributes->get('_route'));
    }

    public function getItems(array $menuTree, string $currentRoute): string
    {
        $menu = '';

        foreach ($menuTree as $item) {
            $activeAttr = $this->setActiveAttrForMainItem($item, $currentRoute);
            $itemLabel = $this->translator->trans($item['label']);

            $liClass = isset($item['sub_items']) ? 'site-menu-item has-sub' : 'site-menu-item';

            $mainElement = !isset($item['icon']) ? "<li class='site-menu-category'>{$itemLabel}"
                : "<li class='{$liClass} {$activeAttr}'>";

            $itemLink = isset($item['sub_items']) ? "javascript:void(0);" : $item['link_attr'];

            if (isset($item['icon'])) {
                $linkElement = "<a href='{$itemLink}'>
                                    <i class='site-menu-icon {$item['icon']}'></i>
                                    <span class='site-menu-title'>{$itemLabel}";

                $arrow_icon = isset($item['sub_items']) ? "<i class='site-menu-arrow'></i>" : '';

                $linkElement .= $arrow_icon . '</span></a>';
            } else {
                $linkElement = '';
            }


            $childrenItems = $this->getSubItems($item, $currentRoute);

            $glueElements = $this->getItemAccess($item) ? $mainElement . $linkElement . $childrenItems . '</li>' : '';
            $menu .= $glueElements;
        }

        return $menu;
    }

    public function getSubItems(array $item, string $currentRoute): string
    {
        $subMenu = '';

        if (isset($item['sub_items'])) {
            $ulElement = "<ul class='site-menu-sub site-menu-sub-up' data-plugin='menu'>";
            $listItems = '';

            foreach ($item['sub_items'] as $subItem) {

                $activeAttr = in_array($currentRoute, $subItem['routes']) ? 'active' : '';

                $itemLabel = $this->translator->trans($subItem['label']);

                if ($this->getItemAccess($subItem)) {

                    $listItems .= "<li class='site-menu-item {$activeAttr}'>
                                      <a href='{$subItem['link_attr']}'>
                                         <span class='site-menu-title'>
                                             {$itemLabel}
                                         </span>  
                                      </a>
                                  </li>";
                }
            }

            $subMenu .= $ulElement . $listItems . '</ul>';
        }

        return $subMenu;
    }


    public function setActiveAttrForMainItem(array $item, string $currentRoute): string
    {
        $activeAttr = '';

        $explodeRoute = explode('.', $currentRoute);

        if (isset($item['routes_id']) && in_array($explodeRoute[0], $item['routes_id'])) {
            $activeAttr = isset($item['sub_items']) ? 'active open' : 'active';
        }

        return $activeAttr;
    }


    public function getItemAccess(array $item): bool
    {
        return true;
        $user = $this->security->getUser();

        if (!isset($item['permissions'])) return true;

        foreach ($item['permissions'] as $permission)
            if ($this->voter->can($user, $permission))
                return true;
        return false;
    }
}
