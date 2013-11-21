<?php
namespace Tsk\UserBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('firstname', 'text')
            ->add('lastname', 'text')
            ->add('submit', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tsk\UserBundle\Entity\User',
            'validation_groups' => array('FormEdit'),
        ));
    }
    /**
     * Returns the name of this type.
     */
    public function getName()
    {
        return "UserEdit";
    }
}
?>