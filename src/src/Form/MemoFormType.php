<?php

namespace App\Form;

use App\Entity\Memo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    '低' => 0,
                    '中' => 1,
                    '高' => 2,
                ],
                'label' => '優先度',
                'required' => true,
                'expanded' => false,
                'placeholder' => '選択してください',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    '着手前' => Memo::STATUS_NOT_STARTED,
                    '進行中' => Memo::STATUS_IN_PROGRESS,
                    '完了' => Memo::STATUS_DONE,
                ],
                'label' => 'ステータス',
                'required' => true,
                'expanded' => false,
                'placeholder' => '選択してください',
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Memo::class,
        ]);
    }
}
