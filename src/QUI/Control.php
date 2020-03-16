<?php

/**
 * This file includes \QUI\Control
 */

namespace QUI;

use QUI;

/**
 * QUI Control
 * PHP counterpart to the \QUI\Control JavaScript class
 *
 * @author www.pcsg.de (Henning Leutz)
 */
class Control extends QDOM implements QUI\Controls\ControlInterface
{
    /**
     * list of css classes
     *
     * @var array
     */
    protected $cssClasses = [];

    /**
     * @var array
     */
    protected $cssFiles = [];

    /**
     * @var array
     */
    protected $styles = [];

    /**
     * JavaScript QUI Require Module name
     * @var string
     */
    protected $module = '';

    /**
     * @var QUI\Events\Event
     */
    protected $Events;

    /**
     * Constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->Events = new QUI\Events\Event();

        if (!isset($attributes['showTitle']) && !isset($this->attributes['showTitle'])) {
            $attributes['showTitle'] = true;
        }

        if (isset($attributes['styles'])) {
            $this->setStyles($attributes['styles']);
            unset($attributes['styles']);
        }

        if (isset($attributes['class'])) {
            $this->addCSSClass($attributes['class']);
            unset($attributes['class']);
        }

        if (isset($attributes['events'])) {
            $this->addEvents($attributes['events']);
            unset($attributes['events']);
        }

        $this->setAttributes($attributes);
    }

    /**
     * Set the javascript qui module class
     *
     * @param string $module
     */
    protected function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * Return the DOM Node string
     *
     * @return string
     */
    public function create()
    {
        try {
            $this->Events->fireEvent('create', [$this]);
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeDebugException($Exception);
        }


        $body = '';

        try {
            $body = $this->getBody();
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);
        }


        $attributes = $this->getAttributes();
        $params     = '';

        foreach ($attributes as $key => $value) {
            if (\strpos($key, 'data-') === false
                && $this->isAllowedAttribute($key) === false
            ) {
                continue;
            }

            if (\is_object($value) || \is_array($value)) {
                continue;
            }

            $key    = Utils\Security\Orthos::clear($key);
            $params .= ' '.$key.'="'.\htmlentities($value).'"';
        }

        // qui class
        $quiClass = '';

        if ($this->getAttribute('qui-class')) {
            $quiClass = 'data-qui="'.$this->getAttribute('qui-class').'" ';
        }

        $cssClasses = [];

        if ($this->getAttribute('class')) {
            $cssClasses[] = $this->getAttribute('class');
        }

        $cssClasses = \array_merge(\array_keys($this->cssClasses), $cssClasses);

        if (!empty($cssClasses)) {
            $quiClass .= 'class="'.\implode(' ', $cssClasses).'" ';
        }


        // nodes
        $nodeName = 'div';

        if ($this->getAttribute('nodeName')) {
            $nodeName = $this->getAttribute('nodeName');
        }


        // styles
        if ($this->getAttribute('height')) {
            $this->styles['height'] = $this->cssValueCheck($this->getAttribute('height'));
        }

        if ($this->getAttribute('width')) {
            $this->styles['width'] = $this->cssValueCheck($this->getAttribute('width'));
        }

        // css_inline
        $styles = [];
        $style  = '';

        foreach ($this->styles as $property => $value) {
            $property = \htmlentities($property);
            $value    = \htmlentities($value);

            $styles[] = $property.':'.$value;
        }

        if (!empty($styles)) {
            $style = 'style="'.\implode(';', $styles).'" ';
        }

        try {
            $this->Events->fireEvent('createEnd', [$this]);
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeDebugException($Exception);
        }


        return "<{$nodeName} {$style}{$quiClass}{$params}>{$body}</{$nodeName}>";
    }

    /**
     * Return the inner body of the element
     * Can be overwritten
     *
     * @return string
     * @throws Exception
     */
    public function getBody()
    {
        return '';
    }

    /**
     * Set the binded javascript control
     *
     * @param string $control
     */
    public function setJavaScriptControl($control)
    {
        $this->setAttribute('qui-class', $control);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setJavaScriptControlOption($name, $value)
    {
        $this->setAttribute(
            'data-qui-options-'.$name,
            $value
        );
    }

    /**
     * Add a css class
     *
     * @param string $cssClass
     */
    public function addCSSClass($cssClass)
    {
        if (!\is_string($cssClass)) {
            return;
        }

        if (empty($cssClass)) {
            return;
        }

        //check if is json
        $json = \json_decode($cssClass, true);

        if (\is_array($json)) {
            foreach ($json as $cssClass) {
                if (!isset($this->cssClasses[$cssClass])) {
                    $this->cssClasses[$cssClass] = true;
                }
            }

            return;
        }

        $classes = QUI\ControlUtils::clearClassName($cssClass);
        $classes = \explode(' ', $classes);

        foreach ($classes as $cssClass) {
            if (!isset($this->cssClasses[$cssClass])) {
                $this->cssClasses[$cssClass] = true;
            }
        }
    }


    /**
     * Remove a css class from the CSS list
     *
     * @param string $cssClass
     */
    public function removeCSSClass($cssClass)
    {
        if (isset($this->cssClasses[$cssClass])) {
            unset($this->cssClasses[$cssClass]);
        }
    }

    /**
     * Add a css file to the control
     *
     * @param string $file
     */
    public function addCSSFile($file)
    {
        $this->cssFiles[] = $file;

        Control\Manager::addCSSFile($file);
    }

    /**
     * Return the added css files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return $this->cssFiles;
    }

    /**
     * @param $val
     *
     * @return string
     */
    protected function cssValueCheck($val)
    {
        $val = \trim($val);

        if (empty($val)) {
            return '';
        }

        if (\is_numeric($val)) {
            return (string)$val.'px';
        }

        if (\strpos($val, 'calc(') !== false) {
            return (string)$val;
        }

        $units = [
            'px',
            'cm',
            'mm',
            'mozmm',
            'in',
            'pt',
            'pc',
            'vh',
            'vw',
            'vm',
            'vmin',
            'vmax',
            'rem',
            '%',
            'em',
            'ex',
            'ch',
            'fr',
            'deg',
            'grad',
            'rad',
            's',
            'ms',
            'turns',
            'Hz',
            'kHz'
        ];

        $no   = (int)$val;
        $unit = \str_replace($no, '', $val);

        if (\in_array($unit, $units)) {
            return $no.$unit;
        }

        if (!empty($no) && empty($unit)) {
            return $no.'px';
        }

        return '';
    }

    /**
     * Set inline style to the control
     *
     * @param string $property - name of the property
     * @param string $value - value of the property
     */
    public function setStyle($property, $value)
    {
        $this->styles[$property] = $value;
    }

    /**
     * Set multiple inline styles to the control
     *
     * @param array $styles
     */
    public function setStyles(array $styles)
    {
        foreach ($styles as $property => $value) {
            $this->setStyle($property, $value);
        }
    }

    /**
     * @param $val
     * @return string
     *
     * @deprecated
     */
    protected function _cssValueCheck($val)
    {
        return $this->cssValueCheck($val);
    }

    /**
     * Is the html node attribute allowed
     *
     * @param $attribute
     * @return boolean
     */
    protected function isAllowedAttribute($attribute)
    {
        $list = [
            'disabled' => true,
            'alt'      => true,
            'title'    => true,
            'href'     => true,
            'target'   => true,
            'role'     => true
        ];

        return isset($list[$attribute]);
    }

    /**
     * Return the Project
     *
     * @return \QUI\Projects\Project
     *
     * @throws Exception
     */
    protected function getProject()
    {
        if ($this->getAttribute('Project')) {
            return $this->getAttribute('Project');
        }

        $Project = QUI::getRewrite()->getProject();

        if (!$Project) {
            $Project = QUI::getProjectManager()->get();
        }

        $this->setAttribute('Project', $Project);

        return $Project;
    }

    /**
     * @return Projects\Project
     * @throws Exception
     *
     * @deprecated
     */
    protected function _getProject()
    {
        return $this->getProject();
    }


    //region events

    /**
     * (non-PHPdoc)
     *
     * @param string $event - The type of event (e.g. 'complete').
     * @param callback $fn - The function to execute.
     * @see \QUI\Interfaces\Events::addEvent()
     *
     */
    public function addEvent($event, $fn)
    {
        $this->Events->addEvent($event, $fn);
    }

    /**
     * (non-PHPdoc)
     *
     * @param array $events
     * @see \QUI\Interfaces\Events::addEvents()
     *
     */
    public function addEvents(array $events)
    {
        $this->Events->addEvents($events);
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $event - The type of event (e.g. 'complete').
     * @param callback|boolean $fn - (optional) The function to remove.
     * @see \QUI\Interfaces\Events::removeEvent()
     *
     */
    public function removeEvent($event, $fn = false)
    {
        $this->Events->removeEvent($event, $fn);
    }

    /**
     * (non-PHPdoc)
     *
     * @param array $events - (optional) If not passed removes all events of all types.
     * @see \QUI\Interfaces\Events::removeEvents()
     *
     */
    public function removeEvents(array $events)
    {
        $this->Events->removeEvents($events);
    }

    //endregion
}
