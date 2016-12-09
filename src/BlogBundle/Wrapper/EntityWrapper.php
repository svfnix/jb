<?php

namespace BlogBundle\Wrapper;

/**
 * Created by PhpStorm.
 * User: svf
 * Date: 12/2/16
 * Time: 3:21 PM
 */
class EntityWrapper
{
    public function slugify($text)
    {

        $text = mb_ereg_replace('[^a-zA-z0-9\آ\ا\أ\إ\ب\پ\ت\ث\ج\چ\ح\خ\د\ذ\ر\ز\ژ\س\ش\ص\ض\ط\ظ\ع\غ\ف\ق\ك\ک\گ\ل\م\ن\و\ؤ\ه\ي\ی\ئ\ء]+', '-', $text);
        $text - preg_replace('/[-]+/', '-', $text);

        return $text;
    }
}