<?php

declare(strict_types=1);

namespace Service;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\BSON\ObjectId;

/* Gestion messages MongoDB */

class MongoService
{
    private Collection $collection;

    public function __construct()
    {
        $host     = $_ENV['MONGO_HOST']      ?? 'mongo';
        $port     = $_ENV['MONGO_PORT']      ?? '27017';
        $user     = $_ENV['MONGO_ROOT_USER'] ?? 'root';
        $password = $_ENV['MONGO_ROOT_PASSWORD'] ?? 'rootpassword';
        $database = $_ENV['MONGO_DATABASE']  ?? 'quai_antique_contact';

        $client = new Client("mongodb://{$user}:{$password}@{$host}:{$port}");

        $this->collection = $client
            ->selectDatabase($database)
            ->selectCollection('contact_messages');
    }

    // Insérer message
    
    public function insertContact(array $data): void
    {
        try {
            $this->collection->insertOne($data);
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de l\'insertion du message : ' . $e->getMessage());
        }
    }

    // Récupérer messages 

    public function findAllContacts(): array
    {
        try {
            $cursor = $this->collection->find(
                [],
                ['sort' => ['date_envoi' => -1]]
            );

            $messages = [];
            foreach ($cursor as $doc) {
                $messages[] = [
                    'id'         => (string) $doc['_id'],
                    'nom'        => $doc['nom'] ?? '',
                    'prenom'     => $doc['prenom'] ?? '',
                    'email'      => $doc['email'] ?? '',
                    'message'    => $doc['message'] ?? '',
                    'date_envoi' => $doc['date_envoi'] ?? '',
                ];
            }

            return $messages;

        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la récupération des messages : ' . $e->getMessage());
        }
    }

    // Supprimer message

    public function deleteContact(string $id): void
    {
        try {
            $this->collection->deleteOne(['_id' => new ObjectId($id)]);
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la suppression du message : ' . $e->getMessage());
        }
    }
}
