<?php

namespace BlogBundle\Twig\Extensions;

use DateTime;
use InvalidArgumentException;
use Twig_Extension;
use Twig_Filter_Method;

/**
 * Created by PhpStorm.
 * User: svf
 * Date: 12/5/16
 * Time: 12:30 AM
 */
class BlogExtension extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            'pnum' => new Twig_Filter_Method($this, 'pnum'),
            'enum' => new Twig_Filter_Method($this, 'enum'),
        );
    }

    public function pnum($input)
    {

        $input = str_replace('1', '۱', $input);
        $input = str_replace('2', '۲', $input);
        $input = str_replace('3', '۳', $input);
        $input = str_replace('4', '۴', $input);
        $input = str_replace('5', '۵', $input);
        $input = str_replace('6', '۶', $input);
        $input = str_replace('7', '۷', $input);
        $input = str_replace('8', '۸', $input);
        $input = str_replace('9', '۹', $input);
        $input = str_replace('0', '۰', $input);

        return $input;

    }

    public function enum($input)
    {
        $input = str_replace('۱', '1', $input);
        $input = str_replace('۲', '2', $input);
        $input = str_replace('۳', '3', $input);
        $input = str_replace('۴', '4', $input);
        $input = str_replace('۵', '5', $input);
        $input = str_replace('۶', '6', $input);
        $input = str_replace('۷', '7', $input);
        $input = str_replace('۸', '8', $input);
        $input = str_replace('۹', '9', $input);
        $input = str_replace('۰', '0', $input);

        return $input;

    }

    public function getName()
    {
        return 'blog_extension';
    }
}