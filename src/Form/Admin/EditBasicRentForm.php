<?php

namespace App\Form\Admin;

use App\Controller\Admin\RentalRecipeCrudController;
use App\Entity\RentalRecipe;
use App\Enum\SystemEnum;
use App\Form\DTO\EditBasicRentDto;
use App\Helper\PaymentHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditBasicRentForm extends AbstractType
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RentalRecipe $rentRecipe */
        $rentRecipe = $options['parent'];
        $lastPayment = $rentRecipe->getRecipePayment()->last();
        if (false === $lastPayment) {
            throw new \LogicException('Last payment is null');
        }

        $limitDate = PaymentHelper::createPaymentDate($lastPayment->getValidityFrom(), $lastPayment->getMaturity());

        $builder->add(
            'amount',
            MoneyType::class,
            [
                'currency' => SystemEnum::CURRENCY->value,
            ]
        )
            ->add(
                'percentage',
                PercentType::class,
                [
                    'required' => false,
                    'scale' => 2,
                ]
            )
            ->add(
                'note',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'validityFrom',
                DateType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThan($limitDate),
                    ],
                ]
            )
            ->add(
                'parentId',
                HiddenType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Uložit',
                    'translation_domain' => SystemEnum::TRANSLATION_DOMAIN->value,
                    'back_url' => $this->adminUrlGenerator
                        ->unsetAll()
                        ->setController(RentalRecipeCrudController::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($rentRecipe->getId())
                        ->generateUrl(),
                    'row_attr' => [
                        'class' => 'w-full justify-end pt-20 ',
                    ],
                ]
            )

            ->setMethod('POST');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('data_class', EditBasicRentDto::class)
            ->setDefault('translation_domain', SystemEnum::TRANSLATION_DOMAIN->value)
            ->setRequired('parent')
            ->addAllowedTypes('parent', RentalRecipe::class);
    }
}
