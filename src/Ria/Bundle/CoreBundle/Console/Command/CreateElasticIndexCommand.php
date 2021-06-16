<?php

namespace Ria\Bundle\CoreBundle\Console\Command;

use Exception;
use Elasticsearch\Client;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CreateElasticIndexCommand extends Command
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        protected Client $client,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $params = $this->parameterBag->get('app.elastic_search');

        try {
            $this->client->indices()->delete(['index' => $params['index']]);
            if (is_file(__DIR__ . '/last_elastic_item_time.txt')) {
                unlink(__DIR__ . '/last_elastic_item_time.txt');
            }
        } catch (Exception $exception) {
        }

        $guzzleClient = (new \GuzzleHttp\Client([
            'headers' => ['Content-Type' => 'application/json']
        ]));

        $guzzleClient->put($params['host'] . '/' . $params['index'], [
            'body' => json_encode([
                'settings' => [
                    'index' => [
                        'analysis' => [
                            'analyzer'    => [
                                'morphology'        => [
                                    'type'        => 'custom',
                                    'char_filter' => ['html_strip', 'replace_char_filter'],
                                    'tokenizer'   => 'standard',
                                    'filter'      => ['lowercase', 'asciifolding', 'worddelimiter', 'stopwords'],
                                ],
                                'snowball_analyzer' => [
                                    'type'     => 'snowball',
                                    'language' => 'Russian',
                                ],
                            ],
                            'filter'      => [
                                'worddelimiter' => ['type' => 'word_delimiter'],
                                'stopwords'     => [
                                    'type'      => 'stop',
                                    'stopwords' => 'а', 'без', 'более', 'бы', 'был', 'была', 'были', 'было', 'быть', 'в', 'вам', 'вас', 'весь', 'во', 'вот', 'все', 'всего', 'всех',
                                    'вы', 'где', 'да', 'даже', 'для', 'до', 'его', 'ее', 'если', 'есть', 'еще', 'же', 'за', 'здесь', 'и', 'из', 'или', 'им', 'их', 'к', 'как', 'ко',
                                    'когда', 'кто', 'ли', 'либо', 'мне', 'может', 'мы', 'на', 'надо', 'наш', 'не', 'него', 'нее', 'нет', 'ни', 'них', 'но', 'ну', 'о', 'об', 'однако',
                                    'он', 'она', 'они', 'оно', 'от', 'очень', 'по', 'под', 'при', 'с', 'со', 'так', 'также', 'такой', 'там', 'те', 'тем', 'то', 'того', 'тоже', 'той',
                                    'только', 'том', 'ты', 'у', 'уже', 'хотя', 'чего', 'чей', 'чем', 'что', 'чтобы', 'чье', 'чья', 'эта', 'эти', 'это', 'я', 'və', 'və ya', 'ki', 'yaxud',
                                    'və yaxud', 'amma', 'ancaq', 'çünki', 'lakin', 'bəlkə', 'bəlkə də', 'əgər', 'sanki', 'həm', 'həm də', 'gah', 'gah da'
                                ],
                            ],
                            'char_filter' => [
                                'replace_char_filter' => [
                                    'type'     => 'mapping',
                                    'mappings' => [
                                        'ё => е',
                                        'Ё => Е',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
        ]);

        $guzzleClient->put($params['host'] . '/' . $params['index'] . '/' . $params['type'] . '/_mapping?include_type_name=true', [
            'body' => json_encode([
                    "properties" => [
                        "title"        => [
                            "type"     => "text",
                            "analyzer" => "morphology"
                        ],
                        "description"  => [
                            "type"     => "text",
                            "analyzer" => "morphology"
                        ],
                        "lang"         => [
                            "type" => "keyword",
                        ],
                        'published_at' => [
                            'type'   => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ],
                    ]
                ]
            )
        ]);

        $io->success('Elasticsearch index successfully created.');
        return Command::SUCCESS;
    }
}
