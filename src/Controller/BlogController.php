<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManager;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    // Un commentaire qui commence par avec un '@' est une annotation tres importante, Symfony explique que lorsqu on lancera
    // www.monsite.com/blog, on fera appel a la méthode index()
    // Pas besoin de préciser templates/blog/index.html.twig, Symfony sait ou se trouve les fichiers templates de rendu


    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
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
        // $repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();
        // findAll() est une méthode issue de la claase ArticleRepository qui permet de selectionner l'ensemble de la table
        // (similaire a SELECT * FROM article)

        dump($articles);

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles 
        ]);
        // On envoie les articles selectionnes en BDD sur le navigateur dans le template index.html.twig
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

    /*
        On déclare une route permettant d'insérer un artcile "/blog/new"
        On déclare une route parametrée  '/blog/{id}/edit' permettant de modifier un article

        Si nous envoyons un (id) dans l'URL, SYMFONY est capable d'aller selectionner en BDD les données de l'article, donc l'objet $article n est plus NULL
        Si nous n'envoyons pas d'(id) dans l'URL, a ce moment la l objet $article est bien NULL
    */

     /**
     * @route("/blog/new", name="blog_create")
     * @route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)
    {
        // Initialement méthode create()
        /*
            La classe request est une classe prédéfinie en SYMFONY qui stockent toutes les données véhiculées par les superglobales
            ($_POST, $_GET, $-SERVER etc....)  
            La propriété 'request' représente la superglobale $_POST, les données saisies dans le formulaire sont accessibles via cette
            propriétés, ca renvoi des parameterBag (sac de parametres)
            Pour insérer un nouvel article, nous devons instancier la classe pour avoir un article vide, toute les propriétés private
            ($Title, $content, $image), ils faut donc les remplir, pour cela nous faisons appel au setter

            EntityManagerInterface est une méthode prédéfinie de SYMFONY qui permet de manipuler les lignes de la BDD (INSERT, UPDATE? DELETE)

            persist() est une méthode issue de la classe EntityManagerInterface qui permet de libérer la requete d'insertion,
            c est elle qui envoie véritablement dans le BDD

            RedirectToRoute() est une méthode de SYMFONY qui permet de redirigé vers une route spécifique, dans notre cas on redirige
            apres insertion vers la route blog_snow (avec le dernier id insérer) afin de renvoyer vers le détail de l'article
            qui vient d etre inséré
        */
        dump($request);

   /* if($request->request->count() > 0)
    {
        $article = new article;
        $article->setTitle($request->request->get('title'))
                ->setContent($request->request->get('content'))
                ->setImage($request->request->get('image'))
                ->setCreatedAt(new \DateTime());

                $manager->persist($article);
                $manager->flush();

                dump($article);

                //return $this->redirectToRoute('blog_show', [
                //    'id' => $article->getid()
                //]);
    }
*/
/*
    createFormBuilder() est une méthode prédéfinie de SYMFONY qui permet de creer un formulaire a partir d'une entité,
    dans notre cas de la classe Article, cela permet aussi de dire que le formulaire permettra de remplir léobjet issue de la classe Article $article

    add() est une méthode qui permet de créer les différents champs de formulaire
    getForm() est une méthode qui permet de terminer et de valider le formulaire

    handleRequest() est une méthode qui permet de récupérer dans notre les informations stockés dans $_POST et de remplir notre
    objet $article, plus besoin de faire appel aux setters de la classe Article
*/      

        // Si l objet $article n est pas rempli, cela veut dire que nous n avons pas envoyé d'(id) dans l'URL; alors c est une insertion, on crée un nouvel objet Article
        if(!$article)
        {
            $article = new Article;
        }
        // $article = new article;

        // On observe quand remplissant l'objet $article via les setters, les getters renvoient les données de l'article directement a l intérieur des champs du formulaire
        //$article->setTitle("titre a la con")
        //        ->setContent("Contenu de l'article a la con");

        // On construit le formulaire
        $form = $this->createFormBuilder($article)
                     ->add('title')                                             
                     ->add('content')
                     ->add('image')                       
                     ->getForm(); // Termine le formulaire

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) // Si le formulaire est soumit et est valide
        {   
            if(!$article->getId())
            {
                // Si l article ne possede pas d'(id), cela veut dire que ce n est pas une modification, alors on appel le setteur de la date de création de l'article
                // Si c est une modification, l article possede deja un id, alors on ne modifie pas la date de creation de l'article
                $article->setCreatedAt(new \DateTime());
            }
            

            $manager->persist($article); // persist récupere l objet $article et prépare la requete d'insertion
            $manager->flush(); // flush() libere réellement la requete SQL d'insertion

            // On redirige apres insertion vers le détail de larticle que nous venons d'insérer
             return $this->redirectToRoute('blog_show', [
                   'id' => $article->getid()
                ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView()
        ]);
    }

    // Show() : Méthode permettant d'afficher le détail d'un article

     /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article) // 3
    {
        /*
            Pour selectionner un article dans le BDD, nous utiliserons le principe de route paramétrées
            dans la route, on définit un parametre de type (id)
            Lorsque nous transmettons dans l'URL par exemple une route '/blog/9' , donc on envoie un id connu en BDD dans l URL
            SYMFONY va automatiquement recupéré ce parametre et le transmettre eb argument de la méthode show()
            Cela veut dire que nous avons acces a l'(id) a l intérieur de la méthod show()
            Nous avons besoin pour cela de la classe ArticleRepository afin de pouvoir selectionner en BDD
            la méthode find() est issue de la classe ArticleRepository et permet de selectionner des données en BDD a partir d un parametre de type (id)
            getDoctrine() : l ORM fait le travail pour nous, c est a dire quelle recupere la requete de selection pour l executer en BDD
            et Doctrine recupere le resultat de la requete de selection pour l envoyer dans le controller

            $repo est un objet issu de la classe ArticleRepository, nous avons acces a toute les méthodes déclarées dans cette classe
            (find, findAll, findBy, findOneBy etc...)
        */
        // $repo = $this->getDoctrine()->getRepository(Article::class);

        // $article = $repo->find($id); // 3, on transmet en argument de la méthode find(), le parametre (id) recupéré dans l URL
        // find() : SELECT * FROM articles WHERE id = ... + FETCH

        dump($article);

        return $this->render('blog/show.html.twig',[
            'article' => $article
        ]);
        // On envoie dans le template show.html.twig, les données selectionnées en BDD, c est a dire le détail d un article
        // extract(['article -> $article]) => 'article' devient une variable TWIG dans le template show.html.twig
    }

   
}
/*
    Injection de dépendances

    Dans SYMFONY nous avons un service container, tout ce qui est contenu dans SYMFONY est géré par SYMFONY
    Si nous observons la classe BlogController, nous ne l avons jamais instanciée, c est SYMFONY lui meme qui se charge de l instancier,
    donc il instancie des classes et appel ses fonctions

    Dans SYMFONY, ces objets utiles sont appelés 'services' et chaque service vit a l intérieur d un objet tres special appelé conteneur
    de service. il vous facilite la vie, favorise une architecture solide et super rapide !! 

    La fonction index() a pour role de nous afficher la liste des articles de la BDD et pour fonctionner, elle a donc besoin d un
    repository (requete de selection), quand une fonction a besoin de quelque chose pour fonctionner, on appel ca une dépendance,
    la fonction dépend d un repository pour aller chercher la liste des articles
    
    Donc si nous avons une dépendance, nouspouvons demander a SYMFONY de nous la fournir plutot que la fabriquer nous meme

    La fonction index() ce n est pas nous qui l executons, c est symfony qui le fait pour nous

    Nous devons fournir a la méthode index() en argument, un objet issu de la classe ArticleRepository
*/




