<?php
namespace App\Form;

use App\Entity\User;
use App\Form\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('oldPassword', PasswordType::class, ['required' => true])
           ->add('newPassword', RepeatedType::class, [
               'type' => PasswordType::class,
               'required' => true,
               'invalid_message' => 'Password fields must match',
               'first_options' => ['label' => 'Password'],
               'second_options' => ['label' => 'Confirm password']
           ])
           ->add('edit', SubmitType::class);

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
        ]);
    }
}