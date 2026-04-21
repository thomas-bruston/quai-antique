<?php
declare(strict_types=1);
namespace Controller;
use Core\Controller;
use Core\Session;
use Service\MongoService;

class ContactController extends Controller
{
    private MongoService $mongoService;

    public function __construct()
    {
        $this->mongoService = new MongoService();
    }

    public function form(): void
    {
        $this->render('contact/form', [
            'csrf_token' => Session::generateCsrfToken(),
            'errors'     => Session::getFlash('errors') ?? [],
            'success'    => Session::getFlash('success'),
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $nom     = trim($this->post('nom'));
        $prenom  = trim($this->post('prenom'));
        $email   = trim($this->post('email'));
        $message = trim($this->post('message'));

        $errors = [];
        if (empty($nom))    $errors[] = 'Le nom est obligatoire.';
        if (empty($prenom)) $errors[] = 'Le prénom est obligatoire.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse email invalide.';
        }
        if (empty($message)) $errors[] = 'Le message est obligatoire.';

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect('/contact');
        }

        try {
            $this->mongoService->insertContact([
                'nom'        => $nom,
                'prenom'     => $prenom,
                'email'      => $email,
                'message'    => $message,
                'date_envoi' => date('Y-m-d\TH:i:s'),
            ]);
            Session::setFlash('success', 'Votre message a bien été envoyé.');
            $this->redirect('/contact');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', ['Une erreur est survenue. Veuillez réessayer.']);
            $this->redirect('/contact');
        }
    }

    public function index(): void
    {
        try {
            $messages = $this->mongoService->findAllContacts();
        } catch (\RuntimeException $e) {
            $messages = [];
        }
        $this->render('admin/messages', [
            'messages' => $messages,
            'success'  => Session::getFlash('success'),
            'errors'   => Session::getFlash('errors') ?? [],
        ]);
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        try {
            $this->mongoService->deleteContact($id);
            Session::setFlash('success', 'Message supprimé.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }
        $this->redirect('/admin/messages');
    }
}