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
     * @param array|object $attributes
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
     * Create the meta block
     *
     * @return string
     */
    public function create()
    {
        $result = '';

        foreach ($this->items as $item) {
            $itemprop   = $item['itemprop'];
            $attributes = $item['attributes'];
            $node       = 'meta';
            $html       = '';

            if (\is_object($attributes)) {
                if (\method_exists($attributes, 'create')) {
                    $result .= $attributes->create();
                }

                continue;
            }

            if (isset($attributes['nodeName'])) {
                $node = $attributes['nodeName'];
                unset($attributes['nodeName']);
            }

            if (isset($attributes['html'])) {
                $html = $attributes['html'];
                unset($attributes['html']);
            }


            if (\is_array($attributes)) {
                $a = '';

                foreach ($attributes as $attr => $v) {
                    if (empty($v)) {
                        $a .= $attr.' ';
                        continue;
                    }

                    $a .= $attr.'="'.$v.'" ';
                }

                $attributes = $a;
            } elseif (\is_string($attributes)) {
                $attributes = ' content="'.$attributes.'"';
            } else {
                $attributes = '';
            }

            if (empty($html)) {
                $result .= '<'.$node.' itemprop="'.$itemprop.'" '.$attributes.' />';
                continue;
            }

            $result .= '<'.$node.' itemprop="'.$itemprop.'" '.$attributes.'>';

            if (!empty($html)) {
                $result .= $html;
            }

            $result .= '</'.$node.'>';
        }

        return $result;
    }
}
