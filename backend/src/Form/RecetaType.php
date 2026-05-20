<?php

namespace App\Form;

use App\Entity\Receta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título de la receta',
                'attr' => ['placeholder' => 'Ej: Batido de Proteínas Pro']
            ])
            ->add('categoria', ChoiceType::class, [
                'label' => 'Objetivo / Categoría',
                'choices' => [
                    'Definición 📉' => 'Definición',
                    'Volumen 📈' => 'Volumen',
                    'Snack / Post-Entreno ⚡' => 'Snack',
                    'Saludable 🌱' => 'Saludable',
                ],
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción corta',
                'attr' => ['rows' => 3, 'placeholder' => 'Explica brevemente de qué trata este plato...']
            ])
            ->add('ingredientes', TextareaType::class, [
                'label' => 'Ingredientes (uno por línea)',
                'attr' => ['rows' => 5, 'placeholder' => "Ej:\n- 30g de proteína en polvo\n- 250ml de leche\n- 1 plátano"]
            ])
            ->add('proteinas', IntegerType::class, [
                'label' => 'Proteínas (g)',
                'attr' => ['min' => 0]
            ])
            ->add('kcal', IntegerType::class, [
                'label' => 'Calorías (Kcal)',
                'attr' => ['min' => 0]
            ])
            ->add('tiempo', IntegerType::class, [
                'label' => 'Tiempo de preparación (minutos)',
                'attr' => ['min' => 1]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Receta::class,
        ]);
    }
}