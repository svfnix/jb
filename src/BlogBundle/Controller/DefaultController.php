<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Category;
use BlogBundle\Wrapper\ControllerWrapper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends ControllerWrapper
{

    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('BlogBundle:Default:index.html.twig', [
            'theme' => $this->theme()
        ]);
    }

    /**
     * @Route("/category/{slug}", name="category_url")
     * @param Category $category
     * @return Response
     */
    public function categoryAction(Category $category)
    {
        return new Response($category->getTitle());
    }

    /**
     * @Route("/article/{slug}", name="article_url")
     * @param Article $article
     * @return Response
     */
    public function articleAction(Article $article)
    {
        return new Response($article->getTitle());
    }
}
