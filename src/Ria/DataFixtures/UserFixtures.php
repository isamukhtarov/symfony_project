<?php
declare(strict_types=1);

namespace Ria\DataFixtures;

use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\CoreBundle\Entity\Status;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\UserBundle\Entity\Translation as UserTranslation;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordEncoderInterface $passwordEncoder,
        private SluggerInterface $slugger,
        private ParameterBagInterface $parameterBag,
    ) {}

    public function load(ObjectManager $manager)
    {
        $rootEmail = 'root@ria.az';
        $rootPassword = 'secret';

        $userAdmin = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $rootEmail]);

        if (!$userAdmin) {
            $userAdmin = new User();
            $userAdmin
                ->setEmail($rootEmail)
                ->setEmailAdditional($rootEmail)
                ->setGender('male')
                ->setPhone('+994555555555')
                ->setBirthDate(new DateTime())
                ->setStatus(new Status(1))
                ->setPassword($this->passwordEncoder->encodePassword($userAdmin, $rootPassword));

            foreach ($this->parameterBag->get('app.supported_locales') as $locale) {
                $translation = new UserTranslation();

                $translation
                    ->setFirstName('Admin')
                    ->setLastName('Super')
                    ->setPosition('Admin')
                    ->setDescription('')
                    ->setLanguage($locale)
                    ->setMeta(new Meta('', '', ''));

                $slug = $this->slugger->slug(mb_strtolower($translation->getFullName(), 'UTF-8'));
                $translation->setSlug($slug->toString());

                $userAdmin->addTranslation($translation);
            }

            $this->entityManager->persist($userAdmin);
            $this->entityManager->flush();
        }

        $this->addReference(self::ADMIN_USER_REFERENCE, $userAdmin);
    }
}