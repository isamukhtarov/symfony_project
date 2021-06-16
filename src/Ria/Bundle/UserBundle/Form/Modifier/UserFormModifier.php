<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Modifier;

use JetBrains\PhpStorm\ArrayShape;
use Ria\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Ria\Bundle\UserBundle\Command\User\UpdateUserCommand;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Ria\Bundle\UserBundle\Command\User\AbstractUserCommand;

class UserFormModifier
{
    private const MODIFIED_FIELDS = ['roles', 'permissions'];
    private bool $isUpdatingProcessed = false;

    public function modifyFields(FormInterface $form, AbstractUserCommand $command): void
    {
        $this->isUpdatingProcessed = ($command instanceof UpdateUserCommand);

        foreach (self::MODIFIED_FIELDS as $field) {
            $options = call_user_func([$this, $field], $command);
            $form->add($field, ChoiceType::class, $options);
        }
    }

    public function roles(AbstractUserCommand $command): array
    {
        $choices = $this->getChoiceOptions($command->getRoles()) + ['label' => 'form.roles'];
        if ($this->isUpdatingProcessed)
            $this->populateChoiceAttributes('roles',$choices, $command->getUser());
        return $choices;
    }

    public function permissions(AbstractUserCommand $command): array
    {
        $choices = $this->getChoiceOptions($command->getPermissions()) + ['label' => 'form.permission'];
        if ($this->isUpdatingProcessed)
            $this->populateChoiceAttributes('permissions', $choices, $command->getUser());
        return $choices;
    }

    private function populateChoiceAttributes(string $field, array &$choices, User $user): void
    {
        $data = match ($field) {
            'roles' => $user->getRolesRelation(),
            'permissions' => $user->getPermissions(),
        };

        $choices['choice_attr'] = fn ($choice) => ($data->contains($choice)) ? ['checked' => 'checked'] : [];
    }

    #[ArrayShape(['multiple' => "bool", 'expanded' => "bool", 'choices' => "array", 'choice_value' => "string", 'choice_label' => "string", 'data' => "array"])]
    private function getChoiceOptions(array $choices): array
    {
        return [
            'multiple' => true,
            'expanded' => true,
            'choices' => $choices,
            'choice_value' => 'id',
            'choice_label' => 'name',
            'data' => [],
        ];
    }
}