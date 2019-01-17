<?php
/**
 * Created by PhpStorm.
 * User: Igorro
 * Date: 17.01.2019
 * Time: 00:27
 */

namespace App\Service\Chart\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

final class ImportData extends AbstractType
{

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('chart_title', TextType::class)
			->add('source', UrlType::class)
			->add('x_path', TextType::class)
			->add('x_name', TextType::class)
			->add('y_path', TextType::class)
			->add('y_name', TextType::class)
			->add('predicted_count', TextType::class)
			->add('save', SubmitType::class)
		;
	}

}