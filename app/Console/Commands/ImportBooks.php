<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Livre;

class ImportBooks extends Command
{
    protected $signature = 'import:books';
    protected $description = 'Import books data from Google Books API';

    public function handle()
    {
        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => 'subject:english',
            'langRestrict' => 'en',
            'maxResults' => 40,
        ]);

        $books = $response->json('items');

        foreach ($books as $book) {

        // $response = Http::timeout(30)->get(
        // 'https://www.googleapis.com/books/v1/volumes',
        // ['q' => 'programming']
        // );

        // if (!$response->ok()) {
        //     $this->error('Impossible de récupérer les livres');
        //     return Command::FAILURE;
        // }

        // $books = $response->json()['items'] ?? [];

        // if (empty($books)) {
        //     $this->warn('Aucun livre trouvé');
        //     return Command::SUCCESS;
        // }

        // foreach ($books as $book) {

            $title = $book['volumeInfo']['title'];

            $pdfPath = $this->generateFakePdf($title);

            Livre::create(
                [
                    'nomL' => $title,
                    'categorieL' => random_int(1,9),
                    'description' => $book['volumeInfo']['description'] ?? 'Description non disponible',
                    'path' => $pdfPath,
                    'id_vendeur' => random_int(1,10),
                    'prixL' => random_int(0,8500),
                    'date' => now()->format('Y-m-d'),
                ]
            );
        }

        $this->info('Books imported successfully!');
    }

    protected function generateFakePdf($title)
    {
        $filename = Str::slug($title).'_'.time().'.pdf';

        $path = 'livres/'.$filename;

        $content = "Fake PDF content for book: ".$title;

        Storage::disk('public')->put($path, $content);

        return $path;
    }
}