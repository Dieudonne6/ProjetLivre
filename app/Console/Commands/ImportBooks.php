<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Livre;
use App\Models\Categorie;

class ImportBooks extends Command
{
    protected $signature = 'import:books';
    protected $description = 'Import books data from an external API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => 'subject:english',
            'langRestrict' => 'en',
            'maxResults' => 40,
        ]);

        $books = $response->json('items');

        foreach ($books as $book) {
            Livre::updateOrCreate(
                // ['titre' => $book['volumeInfo']['title']],
                [
                    'nomL' => $book['volumeInfo']['title'],
                    'categorieL' => random_int(1, 9),
                    'description' => $book['volumeInfo']['description'] ?? 'Description non disponible', // Ajouter la description
                    'path' => $this->generatePdfContent('Contenu du PDF pour ' . $book['volumeInfo']['title']),
                    'id_vendeur' => random_int(1, 10),
                    'statutL' => random_int(0, 1),
                    'prixL' => random_int(0, 8500),
                    'date' => now()->format('Y-m-d'),
                ]
            );
        }

        $this->info('Books imported successfully!');
    }

    protected function getCategorieId($categorieName)
    {
        return Categorie::firstOrCreate(['nom' => $categorieName])->id;
    }

    protected function generatePdfContent($content)
    {
        // Créez un contenu PDF factice (remplacez par votre logique PDF si nécessaire)
        // Ici nous utilisons une chaîne simple pour l'exemple, vous pouvez remplacer cette logique par du vrai contenu PDF
        $pdfContent = "PDF Content: " . $content;

        // Encode le contenu en base64 pour le stockage en base de données
        return base64_encode($pdfContent);
    }
}