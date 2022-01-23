<?php

namespace CS\CustomersPlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityRepository;



class CustomersServiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /* On met pas les services gratuits parcontre ils doivent directement être ajouter aux abonnements de l'Ecole */
        $builder
                    ->add('customersServices', EntityType::class, array(
                    'class'=>'CSPlatformHandlingBundle:CustomersService',
                    'query_builder' => 
                    function(EntityRepository $er)
                    {
                        return $er->createQueryBuilder('c')
                        
                        /* Attention ceci n'est qu'un test la condition doit être
                        ->where('c.published=true AND c.isActive=true AND c.price>0') */
                        ->where('c.published=true AND c.isActive=true')
                        ->orderBy('c.name');
                    },
                    'expanded'=>true,
                    'multiple'=>true,
                    'choice_label' => 'name'
                ));
        

    }
    

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cs_customersplatformbundle_customers_service';
    }


}
