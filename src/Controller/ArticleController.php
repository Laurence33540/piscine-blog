<?php

namespace App\Controller;

use App\Entity\Article;
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


// méthode 2
			// avec le constructor
            //$article= new Article(); new créer une nouvelle instance de classe
            //Article est une entité

			$article = new Article($title, $content, $description, $image);

			// j'utilise la classe entityManager de symfony
			// pour récupérer toutes les valeurs de l'entité Article créée
			// et les enregistrer dans la table article correspondante à l'entité (via SQL insert)
			$entityManager->persist($article);
			$entityManager->flush();
		}

		return $this->render('create-article.html.twig');
	}

}