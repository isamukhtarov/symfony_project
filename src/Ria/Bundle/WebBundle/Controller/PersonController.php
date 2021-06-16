<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PersonBundle\Query\Repositories\PersonRepository;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PersonController
 * @package Ria\Bundle\WebBundle\Controller
 */
class PersonController extends AbstractController
{

    public function __construct(
        protected PersonRepository $personRepository,
        private PostRepository $postRepository,
    )
    {
    }

    #[Route("/persons/", name: "persons", methods: ['GET'], priority: 2)]
    #[Route("/persons/{filter<[A-Z\Ə\Ü\Ş\Ö\Ğ\Ç\İ]{1}>}/", name: "persons_with_filter", methods: ['GET'], priority: 2)]
    public function index(Request $request, ?string $filter = null): Response
    {
        $persons = $this->personRepository->get($request->getLocale(), $filter);

        return $this->render('@RiaWeb/person/persons-list.html.twig', [
            'persons'         => $persons,
            'chars'           => $this->getParameter('alphabet')[$request->getLocale()],
            'filterChar'      => $filter,
            'urlTranslations' => $this->getUrlTranslations()
        ]);
    }

    #[Route("/person/{slug}/", name: "person_view", methods: ['GET'], priority: 2)]
    public function view(Request $request, string $slug): Response
    {
        $person = $this->personRepository->getBySlug($slug, $request->getLocale());
        if (!$person) {
            throw $this->createNotFoundException();
        }

        $person->posts  = $this->postRepository->getByPerson($person->id, $request->getLocale(), 12);;
        $person->photos = $this->personRepository->getPersonPhotos($person->id);

        return $this->render('@RiaWeb/person/person-view.html.twig', [
            'person'          => $person,
            'urlTranslations' => $this->getUrlTranslations($person->id),
        ]);
    }

    protected function getUrlTranslations(?int $personId = null): array
    {
        if (empty($personId)) {
            $route = 'persons';
        } else {
            $route = 'person_view';

            /** @var Person $person */
            $person = $this->personRepository->find($personId);
        }

        $urls = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $params = ['_locale' => $language];

            if (isset($person)) {
                $translation = $person->getTranslation($language);
                $params      += ['slug' => $translation->getSlug()];
            }

            $urls[$language] = $this->generateUrl($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $urls;
    }

}