<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('predicted_count', IntegerType::class)
            ->add('save', SubmitType::class)
        ;
    }
}
