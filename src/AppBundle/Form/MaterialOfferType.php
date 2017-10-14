<?php

namespace AppBundle\Form;

use AppBundle\Entity\MaterialOffers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialOfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'required' => true,
            'label' => 'Name123',
        ));
        $builder->add('url', TextType::class, array(
            'required' => true,
            'label' => 'URL123',
        ));
        $builder->add('price', IntegerType::class, array(
            'required' => true,
            'label' => 'Preis123',
        ));
        $builder->add('shopName', TextType::class, array(
            'required' => true,
            'label' => 'ShopName123',
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MaterialOffers::class,
        ));
    }
}
