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
    public static function parse(Control $Control)
    {
        $result = '';
        $result .= $Control->create();
        $result .= QUI\Control\Manager::getCSS();

        return $result;
    }

    /**
     * Filter CSS class name
     *
     * @param string $cssClass
     * @return string|string[]|null
     */
    public static function clearClassName($cssClass)
    {
        return \trim(\preg_replace('#[^_a-zA-Z0-9-:/]#', ' ', $cssClass));
    }
}
