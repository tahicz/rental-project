<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmitTypeWithBackButtonExtension extends AbstractTypeExtension
{
    /**
     * @return string[]
     */
    public static function getExtendedTypes(): iterable
    {
        return [SubmitType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('back_url')->allowedTypes('string');
    }

    /**
     * @param mixed[] $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (array_key_exists('back_url', $options)) {
            $view->vars['back_url'] = $options['back_url'];
        }
    }
}
