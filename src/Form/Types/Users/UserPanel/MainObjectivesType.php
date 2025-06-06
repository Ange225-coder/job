<?php

    namespace App\Form\Types\Users\UserPanel;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use App\Form\Fields\Users\UserPanel\MainObjectivesFields;

    class MainObjectivesType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('mainObjectives', ChoiceType::class, [
                    'choices' => [
                        'Alternance' => 'Alternance',
                        'Premier emploi' => 'Premier emploi',
                    ],
                    'expanded' => true,
                    'multiple' => true
                ])

                ->add('fields', ChoiceType::class, [
                    'choices' => array_flip([
                        'Agriculture' => 'Agriculture et agro-industrie',
                        'Commerce' => 'Commerce et distribution',
                        'Informatique' => 'Informatique et nouvelles technologies',
                        'Communication' => 'Communication, marketing et publicité',
                        'Finance' => 'Banque, finance et assurance',
                        'Santé' => 'Santé et services médicaux',
                        'Éducation' => 'Éducation et formation',
                        'BTP' => 'Bâtiments et travaux publics (BTP)',
                        'Transport' => 'Transport et logistique',
                        'Énergie' => 'Énergie et mines',
                        'Tourisme' => 'Tourisme et hôtellerie',
                        'Textile' => 'Textile et industrie de la mode',
                        'Artisanat' => 'Artisanat et métiers locaux',
                        'Industrie' => 'Industrie manufacturière',
                        'Immobilier' => 'Immobilier et gestion foncière',
                        'Culture' => 'Arts, culture et divertissement',
                        'Environnement' => 'Environnement et gestion des déchets',
                        'Services' => 'Services divers aux entreprises et particuliers',
                    ]),
                    'expanded' => true,
                    'multiple' => true
                ])
            ;
            
        }


        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => MainObjectivesFields::class,
            ]);
        }
    }