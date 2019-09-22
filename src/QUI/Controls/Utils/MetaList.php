<?php

/**
 * This file includes \QUI\Controls\Utils
 */

namespace QUI\Controls\Utils;

/**
 * Class MetaList
 * - build meta data for controls
 * - <meta itemprop="" content="">
 *
 * @package QUI\Controls\Utils
 */
class MetaList
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param $itemprop
     * @param array $attributes
     */
    public function add($itemprop, $attributes = [])
    {
        $this->items[] = [
            'itemprop'   => $itemprop,
            'attributes' => $attributes
        ];
    }

    /**
     * clears the meta list
     */
    public function clear()
    {
        $this->items = [];
    }

    /**
     * @return string
     */
    public function create()
    {
        $result = '';

        foreach ($this->items as $item) {
            $itemprop   = $item['itemprop'];
            $attributes = $item['attributes'];

            if (\is_array($attributes)) {
                $a = '';

                foreach ($attributes as $attr => $v) {
                    $a .= $attr.'="'.$v.'" ';
                }

                $attributes = $a;
            } elseif (\is_string($attributes)) {
                $attributes = ' content="'.$attributes.'"';
            } else {
                $attributes = '';
            }

            $result .= '<meta itemprop="'.$itemprop.'" '.$attributes.'>';
        }

        return $result;
    }
}