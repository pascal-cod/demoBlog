<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    // Un commentaire qui commence par avec un '@' est une annotation tres importante, Symfony explique que lorsqu on lancera
    // www.monsite.com/blog, on fera appel a la méthode index()
    // Pas besoin de préciser templates/blog/index.html.twig, Symfony sait ou se trouve les fichiers templates de rendu


    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        /*
            Pour selectionner des données en BDD, nous avons besoin de la classe repository de la classe Article
            Une classe Repository permet uniquement de selectionner des données en BDD (requete SQL SELECT)
            On a besoin de l'ORM DOCTRINE pour faire la relation entre la BDD et notre application (getDoctrine())
            getRepository() : méthode issue del'objet DOCTRINE qui permet d'importer une classe Repository (SELECT)

            $repo est un objet issue de la classe ArticleRepository, cette contient des méthodes prédéfinies par SYMFONY permettant de selectionner
            des données en BDD (find, findBy, findOneBy, findAll)

            dump() : équivalent de var_dump(), permet d'observer le résultat de la requete de selection en bas de la page
            dans la barre administrative (cible a droite)
        */
        $repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();
        // findAll() est une méthode issue de la claase ArticleRepository qui permet de selectionner l'ensemble de la table
        // (similaire a SELECT * FROM article)

        dump($repo);

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

     /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenue sur le blog SYMFONY',
            'age' => 25
        ]);
    }

    // Show() : Méthode permettant d'afficher le détail d'un article

     /**
     * @Route("/blog/45", name="blog_show")
     */
    public function show()
    {
        return $this->render('blog/show.html.twig');
    }

    // Créer 1 méthode create() (route'/create') renvoie le template create.html.twig  + un peu de contenu dans le template + test
}
