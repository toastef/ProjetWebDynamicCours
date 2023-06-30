<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Style;
use App\Entity\Tutoriel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TutorielType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => ' Titre du tutos',
            ])
            ->add('description', TextareaType::class, [
                'label' => ' Description du Tutoriel',
            ])
            ->add('content',TextType::class, [
                'label' => 'Lien youtube',
            ])

            ->add('Style', EntityType::class, [
                'class' => Style::class,
                'choice_label' => 'name',
                'placeholder' => 'choisir un Style'
            ])
            ->add('Category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'choisir une Catégorie'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tutoriel::class,
        ]);
    }
}
