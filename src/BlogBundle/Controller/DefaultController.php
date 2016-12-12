<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Article;
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

            $latest_others = $em->getRepository('BlogBundle:Category')->getLatestArticles($item, 10, 0, $ids);
            foreach ($latest_others as $article) {
                $ids[] = $article->getId();
            }

            $now = new DateTime();
            $now->modify('-1 Month');
            $most_viewed = $em->getRepository('BlogBundle:Category')->getMostViewedArticles($item, $now, 7, 0, $ids);

            $categories[] = [
                'category' => $item,
                'latest_with_image' => $latest_with_image,
                'latest_others' => $latest_others,
                'most_viewed' => $most_viewed
            ];
        }

        return $this->render('BlogBundle:Default:index.html.twig', [
            'theme' => $this->theme(),
            'featured' => $em->getRepository('BlogBundle:Article')->getLatestWithImage(8),
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/{slug}/{page}", name="category_url", defaults={"page": 1}, requirements={"page": "\d+"})
     * @param Category $category
     * @param $page
     * @return Response
     */
    public function categoryAction(Category $category, $page)
    {
        $count = 10;
        $start = ($page - 1) * $count;
        if($start < 0){
            $start = 0;
        }

        $em = $this->getDoctrine()->getManager();

        $latest_list = $em->getRepository('BlogBundle:Category')->getLatestArticles($category, $count, $start);
        $latest_with_image = $em->getRepository('BlogBundle:Article')->getLatestWithImage(ceil(count($latest_list) / 2) * 2);

        return $this->render('BlogBundle:Default:category.html.twig', [
            'theme' => $this->theme(),
            'category' => $category,
            'latest_with_image' => $latest_with_image,
            'latest_list' => $latest_list,
            'pagination' => $this->paging($page, $latest_list->count(), $count)
        ]);
    }

    /**
     * @Route("/tag/{slug}/{page}", name="tag_url", defaults={"page": 1}, requirements={"page": "\d+"})
     * @param Tag $tag
     * @param $page
     * @return Response
     */
    public function tagAction(Tag $tag, $page)
    {
        $count = 10;
        $start = ($page - 1) * $count;
        if($start < 0){
            $start = 0;
        }

        $em = $this->getDoctrine()->getManager();

        $latest_list = $em->getRepository('BlogBundle:Tag')->getLatestArticles($tag, $count, $start);
        $latest_with_image = $em->getRepository('BlogBundle:Article')->getLatestWithImage(ceil(count($latest_list) / 2) * 2);

        return $this->render('BlogBundle:Default:tag.html.twig', [
            'theme' => $this->theme(),
            'tag' => $tag,
            'latest_with_image' => $latest_with_image,
            'latest_list' => $latest_list,
            'pagination' => $this->paging($page, $latest_list->count(), $count)
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article_url")
     * @param Article $article
     * @return Response
     */
    public function articleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $latest_with_image = $em->getRepository('BlogBundle:Article')->getLatestWithImage(15);
        $article_before = $em->getRepository('BlogBundle:Article')->getArticleBefore($article);
        $article_after = $em->getRepository('BlogBundle:Article')->getArticleAfter($article);
        $article_related = $em->getRepository('BlogBundle:Article')->getRelatedArticlesByCategory($article, 3);

        return $this->render('BlogBundle:Default:article.html.twig', [
            'theme' => $this->theme(),
            'article' => $article,
            'latest_with_image' => $latest_with_image,
            'article_before' => $article_before,
            'article_after' => $article_after,
            'article_related' => $article_related
        ]);
    }
}
