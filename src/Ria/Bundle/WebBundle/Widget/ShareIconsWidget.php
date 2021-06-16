<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;

/**
 * Class ShareIconsWidget
 * @package Ria\Bundle\WebBundle\Widget
 *
 * @property-read array $data
 */
class ShareIconsWidget extends FrontendWidget
{

    public const BUTTONS = [
        'whatsapp'  => [
            'label'  => 'WhatsApp',
            'dialog' => false,
            'url'    => 'https://api.whatsapp.com/send?text='
        ],
        'facebook'  => [
            'label'  => 'Facebook',
            'dialog' => true,
            'url'    => 'http://www.facebook.com/sharer.php?u='
        ],
        'twitter'   => [
            'label'  => 'Twitter',
            'dialog' => true,
            'url'    => 'https://twitter.com/share?url='
        ],
        'vkontakte' => [
            'label'  => 'Vkontakte',
            'dialog' => true,
            'url'    => 'http://vk.com/share.php?url='
        ],
        'telegram'  => [
            'label'  => 'Telegram',
            'dialog' => true,
            'url'    => 'https://telegram.me/share/url?url='
        ],
        'linkedin'  => [
            'label'  => 'Linkedin',
            'dialog' => true,
            'url'    => 'https://www.linkedin.com/shareArticle?mini=true&url='
        ]
    ];

    public function run(): string
    {
        return $this->render('share-icons.html.twig', [
            'data'       => $this->data,
            'allButtons' => self::BUTTONS
        ]);
    }

}