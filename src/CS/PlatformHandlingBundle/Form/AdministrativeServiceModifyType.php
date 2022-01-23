<?php

namespace CS\PlatformHandlingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use CS\PlatformHandlingBundle\Entity\AdministrativeService;




class AdministrativeServiceModifyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
        ->add('description', TextareaType::class)
        ->add('price', MoneyType::class)
        ->add('save', SubmitType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $service = $event->getData();
            $form = $event->getForm();
    
            /* Si le service est déjà publié aucun autre champs n'est requis */
            if($service->getPublished() == true)
            {
                $form   
                ->add('name', TextType::class, array('disabled'=> true))
                ->add('code', TextType::class, array('disabled'=> true))
                ->add('published', CheckboxType::class, array('disabled'=> true));
                return;
            }

            /* Sinon on peut tout modifier */
            $form
            ->add('name', TextType::class)
            ->add('code', TextType::class)
            ->add('published', CheckboxType::class, array('required'=> false));
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CS\PlatformHandlingBundle\Entity\AdministrativeService'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cs_platformhandlingbundle_administrativeservice';
    }


}
