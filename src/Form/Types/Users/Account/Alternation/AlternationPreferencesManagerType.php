<?php

    namespace App\Form\Types\Users\Account\Alternation;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use App\Form\Fields\Users\Account\Alternation\AlternationPreferencesManagerFields;

    class AlternationPreferencesManagerType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('alternationPreferences', ChoiceType::class, [
                'choices' => [
                    'Pas de télétravail' => 'Pas de télétravail',
                    'Télétravail partiel' => 'Télétravail partiel',
                    'Télétravail uniquement' => 'Télétravail uniquement'
                ],
                'expanded' => true,
                'multiple' => true,
            ]);
        }


        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => AlternationPreferencesManagerFields::class,
            ]);
        }
    }