<?php
namespace BlogBundle\DataFixtures\ORM;

use BlogBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Created by PhpStorm.
 * User: svf
 * Date: 12/7/16
 * Time: 8:39 PM
 */
class LoadCategories extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setTitle('موبایل و تبلت');
        $category->setSlug('موبایل و تبلت');
        $category->setColor('blue');
        $manager->persist($category);
        $this->addReference('cat-0', $category);

        $category = new Category();
        $category->setTitle('لپتاپ و رایانه');
        $category->setSlug('لپتاپ و رایانه');
        $category->setColor('orange');
        $manager->persist($category);
        $this->addReference('cat-1', $category);

        $category = new Category();
        $category->setTitle('بازی و نرم افزار');
        $category->setSlug('بازی و نرم افزار');
        $category->setColor('red');
        $manager->persist($category);
        $this->addReference('cat-2', $category);

        $category = new Category();
        $category->setTitle('تکنولوزی و فن آوری');
        $category->setSlug('تکنولوژی و فن آوری');
        $category->setColor('green');
        $manager->persist($category);
        $this->addReference('cat-3', $category);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}