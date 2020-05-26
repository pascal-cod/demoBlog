<?php

namespace App\DataFixtures;

use App\Entity\Article;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++) // On boucle 10fois afin de créer 10 articles
        {
            $article = new Article; // On instancie la classe Article afin de renseigner les propiétés private et d envoyer les objets type Article


            // On renseigne tout les setteurs de la classe Article afin d ajouter des titres, du contenu etcc qui seront insérés en BDD
            $article->setTitle("titre de l'article n° $i")
                    ->setContent("<p>contenu de l'article n° $i</p>")
                    ->setImage("https://picsum.photos/250")
                    ->setCreatedAt(new DateTime());

            $manager->persist($article); // Persist() est une méthode issue de la classe ObjectManager permettant de garder en mémoire
                                        // les objets Articles crées, il les fait persister dans le temps
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush(); // flush() est une méthode issue de la classe ObjectManager qui permet de générer l'insertion en BDD
    }
}

