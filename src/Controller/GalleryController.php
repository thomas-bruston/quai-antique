<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Entity\Gallery;
use Repository\GalleryRepository;

/* Affichage galerie + CRUD admin */

class GalleryController extends Controller
{
    private GalleryRepository $galleryRepository;

    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const MAX_SIZE           = 5 * 1024 * 1024;
    private const UPLOAD_DIR         = __DIR__ . '/../../public/images/captions/';

    public function __construct()
    {
        $this->galleryRepository = new GalleryRepository();
    }

    // Vue user

    public function index(): void
    {
        $this->render('gallery/index', [
            'photos' => $this->galleryRepository->findAll(),
        ]);
    }

    // Vue admin

    public function admin(): void
    {
        $this->render('admin/gallery', [
            'photos'     => $this->galleryRepository->findAll(),
            'csrf_token' => Session::generateCsrfToken(),
            'success'    => Session::getFlash('success'),
            'errors'     => Session::getFlash('errors') ?? [],
        ]);
    }

    // Ajouter une photo

    public function store(): void
    {
        $this->verifyCsrf();

        $titre = trim($this->post('titre'));

        if (empty($titre)) {
            Session::setFlash('errors', ['Le titre est obligatoire.']);
            $this->redirect('/admin/galerie');
        }

        $photoData = $this->processUpload();

        if (is_array($photoData)) {
            Session::setFlash('errors', $photoData);
            $this->redirect('/admin/galerie');
        }

        try {
            $this->galleryRepository->create(new Gallery($titre, $photoData));
            Session::setFlash('success', 'Photo ajoutée.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/galerie');
    }

    // Modifier une photo

    public function update(string $id): void
    {
        $this->verifyCsrf();

        $photo = $this->galleryRepository->findById((int) $id);

        if (!$photo) {
            $this->redirect('/admin/galerie');
        }

        $titre = trim($this->post('titre'));

        if (empty($titre)) {
            Session::setFlash('errors', ['Le titre est obligatoire.']);
            $this->redirect('/admin/galerie');
        }

        $photo->setTitre($titre);

        // Nouvelle photo uploadée ?
        if (!empty($_FILES['photo']['name'])) {
            $photoData = $this->processUpload();

            if (is_array($photoData)) {
                Session::setFlash('errors', $photoData);
                $this->redirect('/admin/galerie');
            }

            $photo->setPhoto($photoData);
        }

        try {
            $this->galleryRepository->update($photo);
            Session::setFlash('success', 'Photo modifiée.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/galerie');
    }

    // Supprimer une photo

    public function delete(string $id): void
    {
        $this->verifyCsrf();

        $photo = $this->galleryRepository->findById((int) $id);

        try {
            $this->galleryRepository->delete((int) $id);
            // Supprimer le fichier du serveur
            if ($photo) {
                $this->deleteFile($photo->getPhoto());
            }
            Session::setFlash('success', 'Photo supprimée.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/galerie');
    }

    // Traitement de l'upload (retourne les bytes ou un tableau d'erreurs)

    private function processUpload(): string|array
    {
        if (empty($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['Aucune photo sélectionnée.'];
        }

        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            return ['Erreur lors de l\'upload de la photo.'];
        }

        if ($_FILES['photo']['size'] > self::MAX_SIZE) {
            return ['La photo ne doit pas dépasser 5 Mo.'];
        }

        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return ['Format non autorisé. Utilisez JPEG, PNG ou WebP.'];
        }

        // Générer un nom de fichier unique
        $filename = uniqid('gallery_', true) . '.' . $extension;
        $destination = self::UPLOAD_DIR . $filename;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
            return ['Impossible de sauvegarder la photo.'];
        }

        return $filename;
    }

    private function deleteFile(string $filename): void
    {
        $path = self::UPLOAD_DIR . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
