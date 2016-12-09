<?php

namespace BlogBundle\Command;

use BlogBundle\Wrapper\AppCommand;
use BlogBundle\Entity\Article;
use Goutte\Client;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class MinerZoomitCommand extends AppCommand
{
    protected function configure()
    {
        $this
            ->setName('miner:zoomit')
            ->setDescription('crawl zoomit')
        ;
    }

    private function clean($content){

        $content = $this->clearContent($content, ['zoomit.ir']);

        if (!empty($content)) {
            $content = preg_replace_callback(
                '/<div class=\"demo-gallery\">(.*?)<\/div>/si',
                function ($find) {
                    $crawler = new Crawler($find[0]);
                    $pretty_photo = $crawler->filter('li')->each(function ($li, $j) {
                        $img = $li->filter('img')->first();
                        return '<li><a rel="prettyPhoto" href="' . $li->attr('data-src') . '"><img src="' . $img->attr('src') . '" width="100%"/></a></li>';
                    });

                    return '<ul class="sm-gallery">' . implode("\n", $pretty_photo) . '</ul>';
                }, $content);
        }


        $content = preg_replace('#<div.*?مقالات مرتبط.*?</div>#', '', $content);

        return $content;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Zoomit - start crawling ...");

        $this->startClient();
        $this->em = $this->getContainer()->get('doctrine')->getEntityManager();

        $query = $this->em->createQuery("SELECT s FROM BlogBundle:Setting s WHERE s.name = 'zoomit_last_crawled_id'");
        $setting = $query->getSingleResult();
        $last_id = $setting->getValue();

        $crawler = $this->client->request('GET', 'http://www.zoomit.ir/feed/');
        $crawler->filter('item')->each(function($item) use ($output, $setting, $last_id){

            try {
                $title = $item->filter('title')->text();
                $link = $item->filter('link')->text();
                $owner = explode('/', $link);
                if ($owner[2] == 'www.zoomit.ir') {

                    $article_id = explode('/', $link);
                    $article_id = intval($article_id[6]);

                    if ($article_id > $last_id) {

                        $output->writeln(" - item found: " . $title);

                        $image = '';
                        $description = $item->filter('description')->text();
                        preg_match('/<img.*?src="([^"]+)"[^>]+>/i', $description, $images);
                        if (isset($images[1])) {
                            $image = $images[1];
                        }

                        $client = new Client();
                        $crawler = $client->request('GET', $link);
                        $excerpt = $crawler->filter('.article-summery')->first()->text();

                        $content = $crawler->filter('.article-section')->first()->html();
                        $content = $this->clean($content);

                        $tags = [];
                        $crawler->filter('.article-tag-row')->filter('a')->each(function ($a) use (&$tags) {
                            $tags[] = $a->text();
                        });

                        $file_name = $this->getRoot() . '/var/cache/zoomit.tmp';
                        file_put_contents($file_name, file_get_contents($image));
                        if (in_array(exif_imagetype($file_name), [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG])) {


                            $article = new Article();
                            $article->setTitle($title);
                            $article->setSlug($title);
                            $article->setExcerpt($excerpt);
                            $article->setContent($content);

                            $this->addCategories($article);
                            $this->addTags($article, $tags);
                            $this->setImages($article, $file_name);

                            $article->setSource('زومیت');
                            $article->setSourceUrl($link);

                            $this->em->persist($article);

                            if ($article_id > $setting->getValue()) {
                                $setting->setValue($article_id);
                                $this->em->merge($setting);
                            }

                            $this->em->flush();
                        }

                    }
                }
            }catch (\Exception $e){
                $output->writeln(" * Exception: " . $e->getMessage());
                return false;
            }

        });

        $output->writeln("finished.");
    }

}
