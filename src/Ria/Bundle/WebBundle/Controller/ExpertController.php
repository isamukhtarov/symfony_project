<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Ria\Bundle\CoreBundle\Component\CommandValidator\TimestampCommand;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PersonBundle\Query\Repositories\PersonRepository;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ExpertController
 * @package Ria\Bundle\WebBundle\Controller
 */
class ExpertController extends AbstractController
{

    protected const POSTS_COUNT = 20;

    public function __construct(
        protected PersonRepository $personRepository,
        protected PostRepository $postRepository
    )
    {
    }

    #[Route("/expert/{slug}/", name: "expert_view", methods: ['GET'], priority: 2)]
    public function view(Request $request, string $slug): Response
    {
        $expert = $this->personRepository->getExpertBySlug($slug, $request->getLocale());
        if (!$expert) {
            throw $this->createNotFoundException();
        }

        return $this->render('@RiaWeb/index/expert.html.twig', [
            'expert'          => $expert,
            'posts'           => $this->postRepository->getByExpert($expert->id, $request->getLocale(), self::POSTS_COUNT),
            'urlTranslations' => $this->getUrlTranslations($expert->id)
        ]);
    }

    #[Route("/expert-ajax/{expertId<\d+>}/", name: "expert_ajax", methods: ['GET'], priority: 2)]
    public function expertAjax(Request $request, CommandValidatorInterface $validator, int $expertId): JsonResponse
    {
        $timestamp = $request->get('timestamp');
        if (!$validator->validate(new TimestampCommand(), source: $request)) {
            throw $this->createNotFoundException();
        }

        $posts = $this->postRepository->getByExpert($expertId, $request->getLocale(), self::POSTS_COUNT, $timestamp);

        return new JsonResponse([
            'html'         => $this->renderView('@RiaWeb/index/partials/_category-post-item.html.twig', compact('posts')),
            'limitReached' => count($posts) < self::POSTS_COUNT
        ]);
    }

    protected function getUrlTranslations(int $expertId): array
    {
        /** @var Person $person */
        $person = $this->personRepository->find($expertId);

        $urlTranslations = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $translation = $person->getTranslation($language);

            $urlTranslations[$translation->language] = $this->generateUrl('expert_view',
                ['slug' => $translation->getSlug(), '_locale' => $translation->getLanguage()],
                UrlGeneratorInterface::ABSOLUTE_URL);
        }
        return $urlTranslations;
    }

}