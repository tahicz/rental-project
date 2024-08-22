<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminDashboardController extends AbstractDashboardController
{
	#[Route('/admin', name: 'admin')]
	public function index(): Response
	{
		return $this->render('admin/main_dashboard.html.twig');
	}

	public function configureDashboard(): Dashboard
	{
		return Dashboard::new()
			->setTitle('EstateRent')
			->setFaviconPath('favicon.svg')
			->setTranslationDomain('estate-rent-admin')
			->setTextDirection('ltr')
			->renderContentMaximized()
			->generateRelativeUrls()
			;
	}

	public function configureMenuItems(): iterable
	{
		yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
	}
}
