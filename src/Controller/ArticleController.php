<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ArticleController extends AbstractController {

	#[Route('/create-article', name: "create-article")]
	public function displayCreateArticle(Request $request, EntityManagerInterface $entityManager) {

		if ($request->isMethod("POST")) {

			$title = $request->request->get('title');
			$description = $request->request->get('description');
			$content = $request->request->get('content');
			$image = $request->request->get('image');

			// méthode 1 (fonction setter)
			// permet de créer un article
			// utiliser les fonctions "set"
			// pour remplir les données de l'instance de classe Article
			//$article = new Article();
			//$article->setTitle($title);
			//$article->setDescription($description);
			//$article->setContent($content);
			//$article->setImage($image);
			//$article->setIsPublished(true);
			//$article->setCreatedAt(new \DateTime());


			// avec le constructor
		    //$article = new Article(); new créer une nouvelle instance de classe
            //Article est une entité
			$article = new Article($title, $content, $description, $image);

				// j'utilise la classe entityManager de symfony 
				// pour récupérer toutes les valeurs de l'entité Article créée
				// et les enregistrer dans la table article correspondante à l'entité (via SQL insert)
				$entityManager->persist($article);
				$entityManager->flush();
			}

			// je retourne le rendu du fichier à l'utilisateur
			return $this->render('create-article.html.twig');
		}

		// le repository se créer automatiquement en même temps que l'entity (accès aux données).
		#[Route('/list-articles', name: 'list-articles')]
		public function displayListArticles(ArticleRepository $articleRepository) {

			// permet de faire une requête SQL SELECT * sur la table article
			$articles = $articleRepository->findAll();

			return $this->render('list-articles.html.twig', [
				'articles' => $articles
			]);
		}


		// je veux choisir un article avec find
		#[Route('/details-article/{id}', name: "details-article")]
		public function displayDetailsArticle($id, ArticleRepository $articleRepository)
		{

			$article = $articleRepository->find($id);

			// si l'article n'a pas été trouvé pour l'id demandé
			// on envoie l'utilisateur vers la page qui affiche une erreur 404
			if (!$article) {
				return $this->redirectToRoute('404');
			}

			return $this->render('details-article.html.twig', [
				'article' => $article
			]);
		}
 
	// cette méthode prend un id en parametre d'url, 
	// récupère l'article avec le repository 
	// et le supprime avec l'entity manager	
	#[Route('/delete-article/{id}', name: "delete-article")]
	public function deleteArticle($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager) 
	{
		// pour supprimer un article, je dois d'abord le récupérer avec find
		$article = $articleRepository->find($id);

		// j'utilise la méthode remove de la classe EntityManager qui prend en parametre l'article à supprimer et flush l'execute
		$entityManager->remove($article);
		$entityManager->flush();

		// j'ajoute un message flash pour notifier que l'article est supprimé
		$this->addFlash('success', 'article supprimé');

		// je redirige vers la page de liste
		return $this->redirectToRoute('list-articles');
	}


}