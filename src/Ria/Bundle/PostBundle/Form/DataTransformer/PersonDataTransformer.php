<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;

use Ria\Bundle\PersonBundle\Query\Repositories\PersonRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PersonDataTransformer
{
    public function __construct(
        private PersonRepository $personRepository,
        private ParameterBagInterface $parameterBag,
    ){}

    public function transform(array $params = []): array
    {
        $data = [];
        $language = $params['language'] ?? $this->parameterBag->get('app.locale');
        foreach ($this->personRepository->getExperts($language) as $expert)
            $data[$expert['full_name']] = $expert['id'];
        return $data;
    }
}