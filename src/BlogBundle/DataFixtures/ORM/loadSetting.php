<?php
namespace BlogBundle\DataFixtures\ORM;

use BlogBundle\Entity\Category;
use BlogBundle\Entity\Setting;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Created by PhpStorm.
 * User: svf
 * Date: 12/7/16
 * Time: 8:39 PM
 */
class LoadSetting extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $setting = new Setting();
        $setting->setName('zoomit_last_crawled_id');
        $setting->setValue(0);
        $manager->persist($setting);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}