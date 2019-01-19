<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Dto\Chart as ChartDto;

final class ImportData extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chart_title', TextType::class, ['required'=>true])
            ->add('source', UrlType::class, ['required'=>true])
            ->add('x_path', TextType::class, ['required'=>true])
            ->add('x_name', TextType::class, ['required'=>true])
            ->add('y_path', TextType::class, ['required'=>true])
            ->add('y_name', TextType::class, ['required'=>true])
            ->add('predicted_count', IntegerType::class)
        ;
    }

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => ChartDto::class,
		]);
	}
}
