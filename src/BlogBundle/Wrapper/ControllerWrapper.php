<?php
/**
 * Created by PhpStorm.
 * User: svf
 * Date: 12/4/16
 * Time: 11:46 PM
 */

namespace BlogBundle\Wrapper;

use BlogBundle\Entity\Article;
use BlogBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ControllerWrapper extends Controller
{
    public function theme(){

        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository('BlogBundle:Category')->findAll();

        $categories = [];
        foreach ($items as $item){
            $categories[] = [
                'category' => $item,
                'items' => $em->getRepository('BlogBundle:Category')->getLatestArticlesForMegaMenu($item)
            ];
        }
        
        return [
            'categories' => $categories,
            'latest_articles_with_image' => $em->getRepository('BlogBundle:Article')->getLatestWithImage(),
            'latest_articles_text_only' => $em->getRepository('BlogBundle:Article')->getLatestTextOnly(),
        ];
    }
}