<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use DateTime;

/**
 * Type de formulaire pour l'entité Formation.
 */
class FormationType extends AbstractType
{
    /**
     * Construit le formulaire pour l'entité Formation.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre:',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le titre ne peut pas être vide.'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description:',
                'required' => false
            ])
            ->add('videoId', TextType::class, [
                'label' => 'ID Vidéo YouTube:',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'L\'ID de la vidéo ne peut pas être vide.'])
                ]
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'label' => 'Playlist:',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une playlist.'])
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false, // Mettre à true pour checkboxes, false pour select multiple
                'label' => 'Catégories:',
                'required' => false
            ])
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de publication:',
                'required' => true,
                'data' => isset($options['data']) && $options['data']->getPublishedAt() ? $options['data']->getPublishedAt() : new DateTime('now'),
                'constraints' => [
                    new NotBlank(['message' => 'La date de publication ne peut pas être vide.']),
                    new LessThanOrEqual(['value' => 'today', 'message' => 'La date ne peut pas être postérieure à aujourd\'hui.'])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }

    /**
     * Configure les options par défaut pour ce type de formulaire.
     *
     * @param OptionsResolver $resolver Le résolveur d'options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
