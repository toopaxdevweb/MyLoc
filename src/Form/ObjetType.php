<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Objet;
use App\Entity\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('image')
            ->add('proprietaire', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objet::class,
        ]);
    }
}
