<?php
namespace BlogBundle\DataFixtures\ORM;

use BlogBundle\Entity\Keyword;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Created by PhpStorm.
 * User: svf
 * Date: 12/7/16
 * Time: 8:39 PM
 */
class LoadKeywords extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $groups = [
            'موبایل' => [0],
            'تبلت' => [0],
            'کتاب خوان' => [0],
            'گلکسی' => [0],
            'گوشی موبایل' => [0],
            'آی فون' => [0],
            'galaxy' => [0],
            'iphone' => [0],
            'سرفیس' => [0],
            'surface' => [0],
            'فبلت' => [0],
            'لپتاپ' => [1],
            'لپ تاپ' => [1],
            'هارد' => [1],
            'رایانه' => [1],
            'کامپیوتر' => [1],
            'مک بوک' => [1],
            'گیم' => [2],
            'بازی' => [2],
            'نرم افزار' => [2],
            'بد افزار' => [2]
        ];

        foreach($groups as $keyword => $categories) {
            foreach($categories as $category) {
                $object = new Keyword();
                $object->setKeyword($keyword);
                $object->addCategory($this->getReference("cat-{$category}"));
                $manager->persist($object);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}