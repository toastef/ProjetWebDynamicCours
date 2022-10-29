<?php

namespace App\Form;

use App\Entity\Painting;
use App\Entity\Style;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PaintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', TextType::class, [
                'label' => ' Titre de l\'article',
            ])
            ->add('descrioption', TextareaType::class, [
                'label' => ' Description du l\'oeuvre',
            ])
            ->add('height',IntegerType::class,[
                'label' => 'hauteur en cm ',
            ])
            ->add('width',IntegerType::class,[
                'label' => 'Largeur en cm ',

            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Photo',
                'required' => true,
                'download_uri'=> true,
                'image_uri'=> true,
                'asset_helper'=>true,
            ])
            ->add('Style', EntityType::class, [
                'class' => Style::class,
                'choice_label' => 'name',
                'placeholder' => 'choisir un Style'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Painting::class,
        ]);
    }
}
