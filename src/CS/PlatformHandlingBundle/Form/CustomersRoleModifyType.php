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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;




class CustomersRoleModifyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
        ->add('description', TextareaType::class)
        
        ->add('save', SubmitType::class);


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $role = $event->getData();
            $form = $event->getForm();
    
            /* Si le role est déjà publié aucun autre champs n'est requis, on les desactive*/
            if($role->getPublished() == true)
            {
                $form   
                ->add('service', EntityType::class, array(
                    'class'        => 'CSPlatformHandlingBundle:CustomersService',
                    'choice_label' => 'name',
                    'multiple'     => false,
                    'placeholder' => 'Sélectionnez le service concerné !',
                    'disabled' => true
                ))
                ->add('name', TextType::class, array('disabled'=>true))
                ->add('code', TextType::class, array('disabled'=>true))
                ->add('published', CheckboxType::class, array('required'=> false, 'disabled'=> true))
                ->add('foroneuser', CheckboxType::class, array('required'=> false, 'disabled'=> true))
                ->add('managedRoles', EntityType::class, array(
                    'class'        => 'CSPlatformHandlingBundle:CustomersRole',
                    'choice_label' => 'name',
                    'multiple'     => true,
                    'required'     => false,
                    'disabled'     => true
                ));
                return;
            }

            /* Sinon on peut tout modifier */
            $form
            ->add('service', EntityType::class, array(
                'class'        => 'CSPlatformHandlingBundle:CustomersService',
                'choice_label' => 'name',
                'multiple'     => false,
                'placeholder' => 'Sélectionnez le service concerné !'
            ))
            ->add('name', TextType::class)
            ->add('code', TextType::class)
            ->add('published', CheckboxType::class, array('required'=> false))
            ->add('foroneuser', CheckboxType::class, array('required'=> false))
            ->add('managedRoles', EntityType::class, array(
                'class'        => 'CSPlatformHandlingBundle:CustomersRole',
                'choice_label' => 'name',
                'multiple'     => true,
                'required'     => false
            ));
        });

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CS\PlatformHandlingBundle\Entity\CustomersRole'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cs_platformhandlingbundle_customersrole';
    }


}
