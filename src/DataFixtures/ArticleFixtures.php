<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
   public function load(ObjectManager $manager)
   {
       $faker = \Faker\Factory::create('fr_FR');
       // On utilise la bibliotheque FAKER qui permet d 'envoyer des fausses  données aléatoires dans la BDD
       // On a demandé a composer d'installer cette librairie sur notre application

       // Creation de 3 catégories
       for($i = 1; $i <= 3; $i++)
       {    
           // Nous avons besoin d'un objet $category vide afin de renseigner de nouvelles catégories
           $category = new Category;


           // On appel les setteurs de l'entité Category
           $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());

           $manager->persist($category);

           // Creation de 4 a 6 articles
           for($j = 1; $j <= mt_rand(4,6); $j++)
           {    
               // Nous avons besoin d'un objet $article vide afin de créer et d'insérer de nouveaux articles en BDD
               $article = new Article;

                // On demande a FAKER de créer 5 paragraphes aléatoire pour nos nouveaux articles
               $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

               // On renseigne tout les setteurs de la classe Article grace aux méthodes de la librairie FAKER (phrase aléatoire
               // (sentence), image aléatoire (imageUrl() etc....)
               $article->setTitle($faker->sentence())
                       ->setContent($content)
                       ->setImage($faker->imageUrl())
                       ->setCreatedAt($faker->dateTimeBetween('-6 months')) // creation de la date d'article, d'il ya 6 mois a aujourdhui
                       ->setCategory($category); // On renseigne la clé étrangere qui permet de relier les articles aux catégories

                $manager->persist($article);

                // Création e 4 a 6 commentaires
                for($k = 1; $k <= mt_rand(4,10); $k++)
                {
                    $comment = new Comment;

                    $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                    $now =new \DateTime(); // Objet dateTime vec l'heure et la date du jour
                    $interval = $now->diff($article->getCreatedAt()); // représente entre maintenant et la date de creation de l'article (timestamp)
                    $days = $interval->days; // nombre de jour entre maintenant et la date de creation de l'article
                    $mininum = '-' . $days . 'days'; // -100 days entre la date de creation de l'article et maintenant

                $comment->setAuthor($faker->name)
                        ->setContent($content)
                         ->setCreatedAt($faker->dateTimeBetween($mininum))
                         ->setArticle($article); // on relie (clé étrangere) nos commentaires aux articles

                $manager->persist($comment);

                }
           }            
       }
       $manager->flush();

   }

}
   
    /* public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++) // On boucle 10fois afin de créer 10 articles
        {
            $article = new Article; // On instancie la classe Article afin de renseigner les propiétés private et d envoyer les objets type Article


            // On renseigne tout les setteurs de la classe Article afin d ajouter des titres, du contenu etcc qui seront insérés en BDD
            $article->setTitle("titre de l'article n° $i")
                    ->setContent("<p>contenu de l'article n° $i</p>")
                    ->setImage("https://picsum.photos/250")
                    ->setCreatedAt(new \DateTime());

            $manager->persist($article); // Persist() est une méthode issue de la classe ObjectManager permettant de garder en mémoire
                                        // les objets Articles crées, il les fait persister dans le temps
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush(); // flush() est une méthode issue de la classe ObjectManager qui permet de générer l'insertion en BDD
    }*/


