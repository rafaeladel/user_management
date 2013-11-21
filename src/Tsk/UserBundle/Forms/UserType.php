<?php
namespace Tsk\UserBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('plain_password', 'repeated', array(
                'type'              =>  'password',
                'invalid_message'   =>  'The password mush match',
                'required'          =>  true,
                'first_name'        =>  'first',
                'second_name'       =>  'confirmation',
                'first_options'     =>  array('label' => 'Password'),
                'second_options'    =>  array('label' => 'Confirm Password'),
            ))
            ->add('email', 'email')
            ->add('firstname', 'text')
            ->add('lastname', 'text')
            ->add('submit', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tsk\UserBundle\Entity\User',
            'validation_groups' => array('FormCreate'),
        ));
    }

    /**
     * Returns the name of this type.
     */
    public function getName()
    {
        return "User";
    }
}
?>