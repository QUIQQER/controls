<?php

/**
 * This file contains \QUI\Controls\Navigating\Pagination
 */

namespace QUI\Controls\Navigating;

use QUI;

/**
 * Pagination
 *
 * @author  www.pcsg.de (Henning Leutz)
 * @licence For copyright and license information, please view the /README.md
 */
class Pagination extends QUI\Control
{
    /**
     * GET Params
     *
     * @var array
     */
    protected $getParams = [];

    /**
     * URL Params
     *
     * @var array
     */
    protected $urlParams = [];

    /**
     * constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->setAttributes([
            'showLimit'   => false,
            'limits'      => '[10,20,50]',
            'limit'       => 10, // {number} entries per page
            'order'       => false,
            'sheets'      => false, // {number} if false  pages count calculate on the fly
            'count'       => null, // {number} require to calculate sheets on the fly
            'sheet'       => 1, // {number} current (active) page
            'useAjax'     => false,
            'showmax'     => 10,
            'anchor'      => false,
            'opacity'     => 0,
            'useAjaxText' => false
        ]);

        parent::__construct($attributes);

        $this->addCSSFile(
            \dirname(__FILE__).'/Pagination.css'
        );


        if ($this->getAttribute('useAjax')) {
            $this->setAttributes([
                'data-qui' => 'package/quiqqer/controls/bin/navigating/Pagination'
            ]);
        } else {
            $this->setAttribute('data-qui', false);
        }

        $this->setAttribute('class', 'quiqqer-pagination grid-100 grid-parent');
    }

    /**
     * (non-PHPdoc)
     *
     * @see \QUI\Control::create()
     */
    public function getBody()
    {
        try {
            $Engine = QUI::getTemplateManager()->getEngine();
        } catch (QUI\Exception $Exception) {
            return '';
        }

        $Project = false;
        $Site    = $this->getAttribute('Site');

        if (empty($Site)) {
            $Site = false;
        } else {
            $Project = $Site->getProject();
        }

        $count = $this->getAttribute('sheets');


        if ($count === false) {
            if ($this->getAttribute('limit') &&
                $this->getAttribute('count')
            ) {
                $count = \ceil(
                    (int)$this->getAttribute('count') /
                    (int)$this->getAttribute('limit')
                );

                $this->setAttribute('sheets', $count);
            }
        }

        $showmax = $this->getAttribute('showmax');
        $limits  = $this->getAttribute('limits');

        if ($this->getAttribute('useAjax')) {
            $this->setAttribute(
                'data-qui',
                'package/quiqqer/controls/bin/navigating/Pagination'
            );
        } else {
            $this->setAttribute('data-qui', false);
        }

        if ($limits && \is_string($limits)) {
            $limits = \json_decode($limits, true);

            if (!\is_array($limits)) {
                $limits = false;
            }
        }

        $active = $this->getAttribute('sheet');
        $anchor = '';

        if ($this->getAttribute('anchor')) {
            $anchor = $this->getAttribute('anchor');
        }

        if ($showmax >= $count) {
            $showmax = false;
        }

        if (!$showmax) {
            $showmax = $count * 2;
        }

        $gap = \floor($showmax / 2);

        $start = $active - $gap;
        $end   = $active + $gap;

        if ($showmax % 2 === 0) {
            $end--; // -1, weil aktuelle seite nicht mit berechnet werden soll
        }

        if ($start <= 0) {
            $start = 1;
            $end   = $showmax;
        }

        if ($end >= $count) {
            $end   = $count;
            $start = $end - $showmax + 1;

            if ($start <= 0) {
                $start = 1;
            }
        }

        // get params
        $limit = $this->getAttribute('limit');
        $order = $this->getAttribute('order');
        $sheet = $this->getAttribute('sheet');

        $opacity     = $this->getAttribute('opacity');
        $useAjaxText = $this->getAttribute('useAjaxText');

        $this->getParams['sheet'] = $sheet;
        $this->getParams['limit'] = $limit;

        if (!empty($order)) {
            $this->getParams['order'] = $order;
        }

        if ((!$count || $count == 1)
            && $this->getAttribute('limit') === false
        ) {
            return '';
        }

        $this->setAttribute('data-limit', (int)$limit);

        $Engine->assign([
            'this'        => $this,
            'count'       => $count,
            'start'       => $start,
            'end'         => $end,
            'active'      => $active,
            'pathParams'  => $this->urlParams,
            'getParams'   => $this->getParams,
            'anchor'      => $anchor,
            'limit'       => $limit,
            'limits'      => $limits,
            'Site'        => $Site,
            'Project'     => $Project,
            'opacity'     => $opacity,
            'useAjaxText' => $useAjaxText
        ]);

        return $Engine->fetch(\dirname(__FILE__).'/Pagination.html');
    }

    /**
     * Load the GET request variables into the sheet
     */
    public function loadFromRequest()
    {
        $limit = $this->getAttribute('limit');
        $order = $this->getAttribute('order');
        $sheet = $this->getAttribute('sheet');

        if (isset($_GET['limit']) && \is_numeric($_GET['limit'])) {
            $limit = (int)$_GET['limit'];
        }

        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }

        if (isset($_GET['sheet'])) {
            $sheet = (int)$_GET['sheet'];
        } elseif (isset($_GET['page'])) {
            $sheet = (int)$_GET['page'];
        }

        $this->setAttribute('limit', $limit);
        $this->setAttribute('order', $order);
        $this->setAttribute('sheet', $sheet);

        $this->urlParams = QUI::getRewrite()->getUrlParamsList();
    }

    /**
     * Return SQL params
     *
     * @return array
     * @example $this->getSQLParams() : array(
     *     'limit' => '0,20',
     *     'order' => 'field'
     * )
     *
     */
    public function getSQLParams()
    {
        $result = [];

        if ($this->getAttribute('limit')) {
            $result['limit'] = $this->getStart()
                               .','.$this->getAttribute('limit');
        }

        if ($this->getAttribute('order')) {
            $result['order'] = $this->getAttribute('order');
        }

        return $result;
    }

    /**
     * Return the start
     *
     * @return integer
     */
    public function getStart()
    {
        $limit = $this->getAttribute('limit');
        $sheet = $this->getAttribute('sheet');

        return ($sheet - 1) * $limit;
    }

    /**
     * Set GET parameter to the links
     *
     * @param $name
     * @param $value
     */
    public function setGetParams($name, $value)
    {
        $name = QUI\Utils\Security\Orthos::clear($name);

        if (!\is_numeric($value)) {
            $value = QUI\Utils\Security\Orthos::clearFormRequest($value);
        }

        if (empty($value)) {
            if (isset($this->getParams[$name])) {
                unset($this->getParams[$name]);
            }

            return;
        }

        $this->getParams[$name] = urlencode($value);
    }

    /**
     * Set URL parameter to the links
     *
     * @param $name
     * @param $value
     */
    public function setUrlParams($name, $value)
    {
        $name = QUI\Utils\Security\Orthos::clear($name);

        if (!\is_numeric($value)) {
            $value = QUI\Utils\Security\Orthos::clearFormRequest($value);
        }
        
        if (empty($value)) {
            if (isset($this->urlParams[$name])) {
                unset($this->urlParams[$name]);
            }

            return;
        }

        $this->urlParams[$name] = $value;
    }
}
