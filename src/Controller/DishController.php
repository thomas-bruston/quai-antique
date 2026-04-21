<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Entity\Dish;
use Entity\Menu;
use Repository\DishRepository;
use Repository\DishCategoryRepository;
use Repository\MenuRepository;

/* Affichage public de la carte + CRUD admin */

class DishController extends Controller
{
    private DishRepository         $dishRepository;
    private DishCategoryRepository $categoryRepository;
    private MenuRepository         $menuRepository;

    public function __construct()
    {
        $this->dishRepository     = new DishRepository();
        $this->categoryRepository = new DishCategoryRepository();
        $this->menuRepository     = new MenuRepository();
    }

    // Vue user

    public function index(): void
    {
        $this->render('card/index', [
            'dishes' => $this->dishRepository->findAllGroupedByCategory(),
            'menus'  => $this->menuRepository->findAll(),
        ]);
    }

    // Vue admin

    public function admin(): void
    {
        $this->render('admin/card', [
            'categories' => $this->categoryRepository->findAll(),
            'dishes'     => $this->dishRepository->findAll(),
            'menus'      => $this->menuRepository->findAll(),
            'csrf_token' => Session::generateCsrfToken(),
            'success'    => Session::getFlash('success'),
            'errors'     => Session::getFlash('errors') ?? [],
        ]);
    }

    // CRUD Plats

    public function storeDish(): void
    {
        $this->verifyCsrf();

        $titre       = trim($this->post('titre'));
        $description = trim($this->post('description'));
        $prix        = (int) $this->post('prix');
        $categoryId  = (int) $this->post('category_id');

        if (empty($titre) || empty($description) || $prix <= 0 || $categoryId <= 0) {
            Session::setFlash('errors', ['Tous les champs du plat sont obligatoires.']);
            $this->redirect('/admin/carte');
        }

        try {
            $this->dishRepository->create(new Dish($categoryId, $titre, $description, $prix));
            Session::setFlash('success', 'Plat ajouté.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/carte');
    }

    public function updateDish(string $id): void
    {
        $this->verifyCsrf();

        $dish = $this->dishRepository->findById((int) $id);

        if (!$dish) {
            $this->redirect('/admin/carte');
        }

        $titre       = trim($this->post('titre'));
        $description = trim($this->post('description'));
        $prix        = (int) $this->post('prix');
        $categoryId  = (int) $this->post('category_id');

        if (empty($titre) || empty($description) || $prix <= 0 || $categoryId <= 0) {
            Session::setFlash('errors', ['Tous les champs du plat sont obligatoires.']);
            $this->redirect('/admin/carte');
        }

        try {
            $dish->setTitre($titre);
            $dish->setDescription($description);
            $dish->setPrix($prix);
            $dish->setCategoryId($categoryId);
            $this->dishRepository->update($dish);
            Session::setFlash('success', 'Plat modifié.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/carte');
    }

    public function deleteDish(string $id): void
    {
        $this->verifyCsrf();

        try {
            $this->dishRepository->delete((int) $id);
            Session::setFlash('success', 'Plat supprimé.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/carte');
    }

    // CRUD Menus

    public function storeMenu(): void
    {
        $this->verifyCsrf();

        $titre           = trim($this->post('titre'));
        $nombreSequences = (int) $this->post('nombre_sequences');
        $prix            = (int) $this->post('prix');
        $dishIds         = $_POST['dish_ids'] ?? [];

        if (empty($titre) || $nombreSequences <= 0 || $prix <= 0) {
            Session::setFlash('errors', ['Tous les champs du menu sont obligatoires.']);
            $this->redirect('/admin/carte');
        }

        try {
            $menu = new Menu($titre, $nombreSequences, $prix);
            $menu->setDishes(array_map(fn($id) => ['dish_id' => (int)$id], $dishIds));
            $this->menuRepository->create($menu);
            Session::setFlash('success', 'Menu ajouté.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/carte');
    }

    public function updateMenu(string $id): void
    {
        $this->verifyCsrf();

        $menu = $this->menuRepository->findById((int) $id);

        if (!$menu) {
            $this->redirect('/admin/carte');
        }

        $titre           = trim($this->post('titre'));
        $nombreSequences = (int) $this->post('nombre_sequences');
        $prix            = (int) $this->post('prix');
        $dishIds         = $_POST['dish_ids'] ?? [];

        if (empty($titre) || $nombreSequences <= 0 || $prix <= 0) {
            Session::setFlash('errors', ['Tous les champs du menu sont obligatoires.']);
            $this->redirect('/admin/carte');
        }

        try {
            $menu->setTitre($titre);
            $menu->setNombreSequences($nombreSequences);
            $menu->setPrix($prix);
            $menu->setDishes(array_map(fn($id) => ['dish_id' => (int)$id], $dishIds));
            $this->menuRepository->update($menu);
            Session::setFlash('success', 'Menu modifié.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/carte');
    }

    public function deleteMenu(string $id): void
    {
        $this->verifyCsrf();

        try {
            $this->menuRepository->delete((int) $id);
            Session::setFlash('success', 'Menu supprimé.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/carte');
    }
}
