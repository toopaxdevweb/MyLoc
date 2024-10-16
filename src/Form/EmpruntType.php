<?php

namespace App\Form;

use App\Entity\Emprunt;
use App\Entity\Objet;
use App\Entity\user;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('DateDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('DateFin', null, [
                'widget' => 'single_text',
            ])
            ->add('Objet', EntityType::class, [
                'class' => Objet::class,
                'choice_label' => 'id',
            ])
            ->add('Emprunteur', EntityType::class, [
                'class' => user::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emprunt::class,
        ]);
    }
}
