<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nombre', TextType::class, [
                'label' => 'Nombre completo',
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Correo electrónico',
                'attr' => ['class' => 'form-control']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                // 'mapped' => false permite que manejemos la encriptación manualmente en el Controller
                'mapped' => false,
                'required' => $options['is_new'], // Obligatorio solo si es un usuario nuevo
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => $options['is_new'] ? 'Escribe una contraseña' : 'Dejar en blanco para mantener la actual'
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Nivel de acceso (Roles)',
                'choices' => [
                    'Socio / Usuario estándar' => 'ROLE_USER',
                    'Administrador del sistema' => 'ROLE_ADMIN',
                    'Entrenador / Staff' => 'ROLE_COACH',
                ],
                'multiple' => true,   // Los roles en Symfony siempre son un array
                'expanded' => true,   // Cambia el select por Checkboxes (más visual)
                'attr' => ['class' => 'mb-3']
            ])
            ->add('edad', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('peso', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            // Esta opción personalizada nos servirá para saber si el password es obligatorio
            'is_new' => true, 
        ]);
    }
}