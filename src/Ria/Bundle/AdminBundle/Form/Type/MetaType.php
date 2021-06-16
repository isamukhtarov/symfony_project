<?php
declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Form\Type;

use Ria\Bundle\AdminBundle\Command\MetaCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
                'label'    => 'form.meta.title',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'form.meta.description',
                'required' => false
            ])
            ->add('keywords', TextareaType::class, [
                'label'    => 'form.meta.keywords',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MetaCommand::class,
        ]);
    }

}