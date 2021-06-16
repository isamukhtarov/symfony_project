<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PushNotificationService
{
    private const API_URL = 'https://onesignal.com/api/v1/notifications';
    private array $data = [];

    public function __construct(
        private RouterInterface $router,
        private LoggerInterface $logger,
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
        private ParameterBagInterface $parameterBag,
    ){}

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function notify(): bool
    {
        $response = json_decode($this->sendMessage(), true);

        return !empty($response['id']);
    }

    private function sendMessage(): string
    {
        $postUrl = 'https://report.az/' . $this->data['category_slug'] . "/" . $this->data['slug'] . '/';

        // Modify post title, remove html tags and set charset.
        $postTitle = strip_tags(trim(html_entity_decode((string) $this->data['title'], ENT_QUOTES, 'UTF-8')));
        $postTitle = mb_substr($postTitle, 0, 100) . '...';

        $onesignalConfig = $this->parameterBag->get('app.onesignal');
        $data = [
            'app_id'            => $onesignalConfig['app_id'],
            'included_segments' => ['All'],
            'chrome_web_image'  => '',
            'contents'          => ["en" => $postTitle],
            'headings'          => ["en" => $postTitle],
            'url'               => $postUrl,
        ];

        try {
            $response = $this->httpClient->request('POST', self::API_URL, [
                'json' => $data,
                'headers' => [
                    'Content-Type'  => 'application/json; charset=utf-8',
                    'Authorization' => 'Basic ' . $onesignalConfig['rest_api_key'],
                ],
            ]);
            return $response->getContent(false);
        } catch (TransportExceptionInterface | Exception $e) {
            $this->logger->error('Error occurred while sending push notification', compact('e'));
            return json_encode([]);
        }
    }
}