<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Category;
use BlogBundle\Wrapper\ControllerWrapper;
use DateTime;
use Doctrine\ORM\Query\Expr\Literal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends ControllerWrapper
{

    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $items = $em->getRepository('BlogBundle:Category')->findAll();

        $categories = [];
        foreach ($items as $item){

            $ids = [];
            $latest_with_image = $em->getRepository('BlogBundle:Category')->getLatestArticlesWithImage($item, 8);
            foreach ($latest_with_image as $article) {
                $ids[] = $article->getId();
            }

            $latest_others = $em->getRepository('BlogBundle:Category')->getLatestArticles($item, 10, $ids);
            foreach ($latest_others as $article) {
                $ids[] = $article->getId();
            }

            $now = new DateTime();
            $now->modify('-1 Month');
            $most_viewed = $em->getRepository('BlogBundle:Category')->getMostViewedArticles($item, $now, 7, $ids);

            $categories[] = [
                'category' => $item,
                'latest_with_image' => $latest_with_image,
                'latest_others' => $latest_others,
                'most_viewed' => $most_viewed
            ];
        }

        return $this->render('BlogBundle:Default:index.html.twig', [
            'theme' => $this->theme(),
            'featured' => $em->getRepository('BlogBundle:Article')->getLatestWithImage(),
            'categories' => $categories
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
