<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Figure;
use App\Entity\GroupeFigure;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FigureFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        
        for($g = 1; $g <= mt_rand(5,7); $g++ ){
            $groupe = new GroupeFigure;

            $groupe->setTitle( $faker->sentence( mt_rand(2,5) ) )
                   ->setDescription( $faker->paragraph() ); 

            $manager->persist($groupe);

            for($f = 1; $f <= mt_rand(3, 8); $f++){
                $content =  '<p>'. join($faker->paragraphs(6), '</p><p>') . '</p>';

                $dateFigure = $faker->dateTimeBetween('-12 months'); //Date d'insertion de la figure 
                $now = new \DateTime; // Date du jours
                $interval = $now->diff($dateFigure); // Inverval entre la date du jours et la date d'ajout de la figure
                $days = $interval->days; // Différence en nombre de jours en les deux dates
                $minimun = '-' . $days .' days'; // String du minumun pour la date d'update;

                $figure = new Figure(); // Je crée une figure 
                $figure->setTitle($faker->sentence())
                        ->setShortDescription($faker->text(60))
                        ->setContent($content)
                        ->setCreateAt($dateFigure)
                        ->setUpdateAt($faker->dateTimeBetween($minimun))
                        ->setGroupe($groupe); //J'ajoute à la suite toute le contenu de chaque propriété à mon obejet

                $manager->persist($figure); // Je prépare mon manager à le mettre en base de donnée
            }
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
