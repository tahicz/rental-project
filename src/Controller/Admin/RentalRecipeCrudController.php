<?php

namespace App\Controller\Admin;

use App\Entity\RentalRecipe;
use App\Enum\SystemEnum;
use App\Form\Admin\EditBasicRentForm;
use App\Form\DTO\EditBasicRentDto;
use App\Repository\RentalRecipeRepository;
use App\Service\PaymentPlanner;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class RentalRecipeCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly PaymentPlanner $paymentPlanner,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly RentalRecipeRepository $rentalRecipeRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return RentalRecipe::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnDetail();
        yield MoneyField::new('basicRent', 'Basic rent')
            ->setRequired(true)
            ->setCurrency(SystemEnum::CURRENCY->value)
            ->setStoredAsCents(false)
            ->setTemplatePath('admin/field/rental_recipe/detail/basic_rent.html.twig')
            ->formatValue(function (float $basicRent, RentalRecipe $rentalRecipe): array {
                $data = [];
                while ($rentalRecipe instanceof RentalRecipe) {
                    $data[] = $rentalRecipe;
                    $rentalRecipe = $rentalRecipe->getChild();
                }

                return $data;
            })
        ;
        yield CollectionField::new('additionalFees', 'Additional fees')
            ->hideOnIndex()
            ->useEntryCrudForm(AdditionalFeeCrudController::class)
            ->setEntryIsComplex(true)
            ->setTemplatePath('admin/field/rental_recipe/detail/additional_fees.html.twig')
            ->allowAdd()
            ->allowDelete(false)
            ->renderExpanded(true)
            ->setFormTypeOptions([
                'by_reference' => true,
            ]);

        yield IntegerField::new('maturity')
            ->setFormTypeOptions([
                'attr' => [
                    'min' => 1,
                    'max' => 28,
                ],
            ])
            ->setRequired(true);
        yield DateTimeField::new('createdAt')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder(
            $searchDto,
            $entityDto,
            $fields,
            $filters
        );
        $qb->andWhere('entity.parent IS NULL');

        return $qb;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInPlural('Rental recipes')
            ->setEntityLabelInSingular('Rental recipe')
            ->setPageTitle(Crud::PAGE_INDEX, '%entity_label_singular% list')
            ->setPageTitle(Crud::PAGE_DETAIL, '%entity_label_singular% detail');

        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $generateNextPaymentsAction = Action::new('new_payments_action', 'Generate payments recipes', 'fa-solid fa-file-invoice-dollar');
        $generateNextPaymentsAction->linkToCrudAction('generateNewPayments');
        $actions->add(Crud::PAGE_DETAIL, $generateNextPaymentsAction);

        $updateBaseRent = Action::new('update_basic_rent', 'Update basic rent', 'fas fa-edit');
        $updateBaseRent->linkToCrudAction('updateBasicRent');
        $actions->add(Crud::PAGE_DETAIL, $updateBaseRent);

        return $actions;
    }

    public function generateNewPayments(AdminContext $context): Response
    {
        $rentalRecipe = $context->getEntity()->getInstance();
        try {
            $this->paymentPlanner->planFuturePayments($rentalRecipe, 12);
            $this->addFlash('success', 'Generated next 12 payments.');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Some future payments are not generated. Error:'.$exception->getMessage());
        } finally {
            $url = $this->adminUrlGenerator
                ->unsetAll()
                ->setController(RentalRecipeCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($rentalRecipe->getId());

            return $this->redirect($url->generateUrl());
        }
    }

    public function updateBasicRent(AdminContext $context): Response
    {
        /** @var RentalRecipe $entity */
        $entity = $context->getEntity()->getInstance();
        $latestState = $entity->getLatestState();

        /** @var Form $form */
        $form = $this->createForm(
            EditBasicRentForm::class,
            EditBasicRentDto::defaultData($latestState),
            [
                'parent' => $entity,
            ]
        );
        $form->handleRequest($context->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditBasicRentDto $data */
            $data = $form->getData();

            if (null === $data->getPercentage()) {
                $amount = $data->getAmount();
            } else {
                $amount = $latestState->getBasicRent() + ($entity->getBasicRent() * $data->getPercentage());
            }

            $rentalRecipe = new RentalRecipe();
            $rentalRecipe->setBasicRent(round($amount, 2))
                ->setMaturity($entity->getMaturity())
                ->setValidityFrom(\DateTimeImmutable::createFromMutable($data->getValidityFrom()))
                ->setNote($data->getNote())
                ->setParent($latestState);

            $this->rentalRecipeRepository->persist($rentalRecipe);

            $latestState->setValidityTo($rentalRecipe->getValidityFrom()->modify('-1 day'))
                ->setChild($rentalRecipe);

            $this->rentalRecipeRepository->save($latestState);
            $clickedButton = $form->getClickedButton();
            if (!$clickedButton instanceof FormInterface) {
                throw new \RuntimeException();
            }
            $backUrl = $clickedButton->getConfig()->getOption('back_url');
            if (!is_scalar($backUrl)) {
                throw new \RuntimeException();
            }

            return $this->redirect((string) $backUrl);
        }

        return $this->render('admin/action/update_basic_rent.html.twig', [
            'update_basic_rent_form' => $form->createView(),
            'pageName' => Crud::PAGE_EDIT,
            'entity' => $context->getEntity(),
        ]);
    }
}
