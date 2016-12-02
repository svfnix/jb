<?php

namespace FeedBundle\Controller;

use BlogBundle\Entity\Article;
use BlogBundle\Entity\Tag;
use BlogBundle\Form\ArticleType;
use BlogBundle\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    private function image($path){
        $this
            ->get('image.handling')
            ->open($path)
            ->zoomCrop(200, 150)
            ->save($path);
    }

    private function thumbnail($path){
        $this
            ->get('image.handling')
            ->open($path)
            ->zoomCrop(600, 450)
            ->save($path);
    }

    /**
     * @Route("/updater")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {

        $article = new Article();
        $article->setTitle($request->request->get('title'));
        $article->setSlug($request->request->get('title'));
        $article->setExcerpt($request->request->get('excerpt'));
        $article->setContent($request->request->get('content'));
        $article->setSource($request->request->get('source'));
        $article->setSourceUrl($request->request->get('source_url'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        $image = $request->files->get('image');
        switch ($image->getExtension()){
            case 'png':
            case 'jpg':
            case 'jpeg':
                $name = 'image-' . $article->getId() . '.' . $image->getExtension();
                $image->move('images', $name);
                $this->image("images/{$name}");
                $article->setImage($name);
                break;
        }

        $thumbnail = $request->files->get('thumbnail');
        switch ($thumbnail->getExtension()){
            case 'png':
            case 'jpg':
            case 'jpeg':
                $name = 'thumbnail-' . $article->getId() . '.' . $thumbnail->getExtension();
                $thumbnail->move('images', $name);
                $this->thumbnail("images/{$name}");
                $article->setImage($name);
                break;
        }

        $em->persist($article);
        $em->flush();

        $cat = $em->getRepository('BlogBundle:Category')->find((int) $request->request->get('cats'));
        if($cat) {
            $article->addCategory($cat);
            $em->persist($article);
            $em->flush();
        }

        $tags = $request->request->get('tags');
        $tags = explode(',', $tags);
        foreach($tags as $tag) {
            $tag_object = $em->getRepository('BlogBundle:Tag')->findOneBy(['title' => $tag]);
            if(!$tag_object){
                $tag_object = new Tag();
                $tag_object->setTitle($tag);
                $tag_object->setSlug($tag);
                $tag_object->addArticle($article);
            }
            $article->addTag($tag_object);
        }
        $em->persist($article);
        $em->flush();

        return new Response('ok');
    }
}
