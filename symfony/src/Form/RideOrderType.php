<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Driver;
use App\Entity\Payment;
use App\Entity\RideOrder;
use App\Entity\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RideOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'id',
            ])
            ->add('driver', EntityType::class, [
                'class' => Driver::class,
                'choice_label' => 'id',
            ])
            ->add('route', EntityType::class, [
                'class' => Route::class,
                'choice_label' => 'id',
            ])
            ->add('payment', EntityType::class, [
                'class' => Payment::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RideOrder::class,
        ]);
    }
}
