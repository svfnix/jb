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
    /**
     * @return array
     */
    protected function theme(){

        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository('BlogBundle:Category')->findAll();

        $categories = [];
        foreach ($items as $item){
            $categories[] = [
                'category' => $item,
                'items' => $em->getRepository('BlogBundle:Category')->getLatestArticlesWithImage($item)
            ];
        }
        
        return [
            'categories' => $categories,
            'latest_articles' => $em->getRepository('BlogBundle:Article')->getLatestTextOnly(),
        ];
    }

    protected function paging($current, $count, $pp)
    {
        $max = ceil($count / $pp);

        $start = $current - 3;
        if($start < 1){
            $start = 1;
        }

        $end = $current + 3;
        if($end > $max){
            $end = $max;
        }

        return [
            'current' => $current,
            'max' => $max,
            'start' => $start,
            'end' => $end
        ];
    }
}