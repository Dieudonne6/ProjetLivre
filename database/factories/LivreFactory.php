<?php

namespace Database\Factories;

use App\Models\Livre;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livre>
 */
class LivreFactory extends Factory
{
    protected $model = Livre::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'nomL' => $this->faker->name,
            'categorieL' => $this->faker->numberBetween(1, 9),
            'description' => $this->faker->paragraphs(2, true),
            'path' => $this->generatePdfContent($this->faker->paragraphs(3, true)), // Générez un PDF avec le contenu
            'date' => $this->faker->dateTimeBetween('-10 months', 'now'), // Date aléatoire entre 10 ans en arrière et aujourd'hui
            // 'date' => $this->faker->dateBetween(
            //     now()->subMonths(10)->format('Y-m-d'), // Date de début (10 mois en arrière)
            //     now()->format('Y-m-d')                 // Date de fin (aujourd'hui)
            // ),
            'prixL' => $this->faker->numberBetween(0, 8500),
            'id_vendeur' => $this->faker->numberBetween(1, 10),
        ];
    }

        /**
     * Génère un contenu PDF factice et renvoie la chaîne binaire.
     *
     * @param string $content
     * @return string
     */
    protected function generatePdfContent($content)
    {
        // Créez un contenu PDF factice (remplacez par votre logique PDF si nécessaire)
        // Ici nous utilisons une chaîne simple pour l'exemple, vous pouvez remplacer cette logique par du vrai contenu PDF
        $pdfContent = "PDF Content: " . $content;

        // Encode le contenu en base64 pour le stockage en base de données
        return base64_encode($pdfContent);
    }
}
