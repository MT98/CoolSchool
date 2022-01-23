<?php

namespace CS\CustomersPlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use CS\PlatformHandlingBundle\Form\ImageType;



class SchoolType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* on crée un nouveau constructeur de formulaire pour les champs school */
        $schoolForm = $builder->create('school', 'Symfony\Component\Form\Extension\Core\Type\FormType', array(
            'data_class' => 'CS\CustomersPlatformBundle\Entity\School',
        ));
        /* On ajoute les champs d'ecole à ce nouveau constructeur de formulaire */
        $schoolForm->add('name',TextType::class)
                    ->add('acronym', TextType::class)
                    ->add('description', TextareaType::class)
                    ->add('logo', ImageType::class, array('required'=>false))
                    ->add('country', EntityType::class, array(
                        'class'        => 'CSPlatformHandlingBundle:Country',
                        'choice_label' => 'name',
                        'placeholder' => 'Sélectionner le pays où il se trouve'
            
                    ))
                    ->add('type', ChoiceType::class, array('choices' => array('private' => 'private', 'public' => 'public'), 'expanded'=>true, 'multiple'=>false))
                    ->add('levels', ChoiceType::class, array('choices' => array('primary' => 'primary', 'school' => 'school', 'high-school' => 'high-school', 'university' => 'university'), 'expanded'=>true, 'multiple'=>true))
                    ->add('address', TextType::class);
        
        $builder->add($schoolForm);
        
    }


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cs_customersplatformbundle_school';
    }


}
