<?php
namespace BlogBundle\Wrapper;


use BlogBundle\Entity\Article;
use BlogBundle\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;

class AppCommand extends ContainerAwareCommand
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var EntityManager
     */
    protected $em;

    protected function startClient()
    {
        $CookieJar = new CookieJar();

        $this->client = new Client([
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:49.0) Gecko/20100101 Firefox/49.0',
            'Upgrade-Insecure-Requests' => 1
            ], null, $CookieJar
        );
    }

    protected function getRoot()
    {
        return dirname($this->getContainer()->get('kernel')->getRootDir());
    }

    protected function clearContent($content, $filter_href=[])
    {

        $content = preg_replace_callback('/<a.*?href=([\'"])(.*?)\\1[^>]*>(.*?)<\/a>/Si', function($link) use($filter_href){
            foreach($filter_href as $href){
                if(strpos($link[2], $href) != false){
                    return $link[3];
                } else {
                    return '<a href="'.$link[2].'" target="_blank" rel="nofollow">'.$link[3].'</a>';
                }
            }
        }, $content);

        $content = preg_replace_callback('/<img.*?src=([\'"])([^"]+)\\1[^>]+>/si', function($image){
            return '<img src="'.$image[2].'" style="max-width:100%" />';
        }, $content);

        return $content;
    }

    protected function setImages($article, $file_name)
    {

        $uniqid = uniqid();
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $image_path = "images/" . date('Y/m/d');

        switch (exif_imagetype($file_name)){
            case IMAGETYPE_GIF:
                $ext = 'gif';
                break;

            case IMAGETYPE_JPEG:
                $ext = 'jpeg';
                break;

            case IMAGETYPE_PNG:
                $ext = 'png';
                break;
        }

        @mkdir($this->getRoot() . "/web/{$image_path}", 0755, true);

        $image_service = $this
            ->getContainer()
            ->get('image.handling');

        $image_service
            ->open($file_name)
            ->zoomCrop(700, 330)
            ->save($this->getRoot() . "/web/{$image_path}/IMG-{$uniqid}-700x330.{$ext}");

        $image_service
            ->open($file_name)
            ->zoomCrop(351, 185)
            ->save($this->getRoot() . "/web/{$image_path}/IMG-{$uniqid}-351x185.{$ext}");

        $image_service
            ->open($file_name)
            ->zoomCrop(214, 140)
            ->save($this->getRoot() . "/web/{$image_path}/IMG-{$uniqid}-214x140.{$ext}");

        $image_service
            ->open($file_name)
            ->zoomCrop(168, 137)
            ->save($this->getRoot() . "/web/{$image_path}/IMG-{$uniqid}-168x137.{$ext}");

        $image_service
            ->open($file_name)
            ->zoomCrop(150, 150)
            ->save($this->getRoot() . "/web/{$image_path}/IMG-{$uniqid}-150x150.{$ext}");

        $article->setImage("/{$image_path}/IMG-{$uniqid}-{size}.{$ext}");
        $article->setThumbnail("/{$image_path}/IMG-{$uniqid}-150x150.{$ext}");
    }

    protected function addCategories($article, $cats=null)
    {
        if (!empty($cats)) {
            $cats = explode(',', $cats);
            foreach($cats as $cat) {
                $cat = $this->em->getRepository('BlogBundle:Category')->find(intval($cat));
                if ($cat) {
                    $article->addCategory($cat);
                }
            }
        } else {
            $this->em->getRepository('BlogBundle:Keyword')->extractArticleCategories($article);
        }
    }

    protected function addTags(Article $article, $tags)
    {
        foreach($tags as $tag) {
            $tag = trim($tag);
            if(!empty($tag)) {
                $tag_object = $this->em->getRepository('BlogBundle:Tag')->findOneBy(['title' => $tag]);
                if (!$tag_object) {
                    $tag_object = new Tag();
                    $tag_object->setTitle($tag);
                    $tag_object->setSlug($tag);
                }
                $article->addTag($tag_object);
            }
        }
    }

}