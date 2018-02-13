<?php

/**
 * This file contains QUI\ControlUtils
 */

namespace QUI;

use QUI;

/**
 * Control Utils
 *
 * @author www.pcsg.de (Henning Leutz)
 */
class ControlUtils
{
    /**
     * Return the complete html from a php control
     *
     * @param Control $Control
     * @return string
     */
    static public function parse(Control $Control)
    {
        $result = '';
        $result .= $Control->create();
        $result .= QUI\Control\Manager::getCSS();

        return $result;
    }
}