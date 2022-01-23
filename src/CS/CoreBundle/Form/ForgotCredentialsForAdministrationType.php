<?php

namespace CS\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class ForgotCredentialsForAdministrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('what', ChoiceType::class, array(
            'choices'  => array(
                'Sélectionnez ce que vous avez oublié?' => null,
                'Nom d\'utilisateur' => 'username',
                'Mot de passe' => 'password'
            )))
        ->add('email', EmailType::class)     
        ->add('submit', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CS\CoreBundle\Entity\ForgotCredentialsForAdministration',
            'timed_spam' => true,
            'timed_spam_min' => 4,
            'timed_spam_message' => 'Please wait 4 seconds before submitting',
            'honeypot' => true,
            'honeypot_field' => 'email_address',
            'honeypot_use_class' => false,
            'honeypot_hide_class' => 'hidden',
            'honeypot_message' => 'Form field are invalid'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return ; /* this delete all prefix in form name field */
    }


}
