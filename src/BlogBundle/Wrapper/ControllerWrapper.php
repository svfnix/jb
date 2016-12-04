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

        return [
            'categories' => $em->getRepository('BlogBundle:Category')->findAll(),
            'latest_articles' => $em->getRepository('BlogBundle:Article')->findBy([], ['id' => 'DESC'], 10, 0),
        ];
    }
}