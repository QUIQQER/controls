<?php

namespace QUI\Controls\Utils\MetaList;

use QUI;
use QUI\Projects\Media\Utils as MediaUtils;
use QUI\Projects\Project;

/**
 * Class Publisher
 *
 * @package QUI\Controls\Utils\MetaList
 */
class Publisher extends QUI\QDOM
{
    /**
     * Publisher constructor.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * Import the project
     * and use the project publisher data
     *
     * @param Project $Project
     */
    public function importFromProject(Project $Project)
    {
        $publisher = $Project->getConfig('publisher');
        $publisher = \htmlspecialchars($publisher);

        $publisherType = $Project->getConfig('publisher_type');

        $itemScope = 'publisher';
        $itemType  = 'https://schema.org/Organization';

        if ($publisherType === 'person') {
            $itemType = 'https://schema.org/Person';
        }

        $this->setAttribute('itemtype', $itemType);
        $this->setAttribute('itemscope', $itemScope);
        $this->setAttribute('publisher', $publisher);
        $this->setAttribute('logo', $Project->getConfig('publisher_image'));
        $this->setAttribute('url', $Project->getConfig('publisher_url'));
    }

    /**
     * @return string
     */
    public function create()
    {
        $publisher = \htmlspecialchars($this->getAttribute('publisher'));
        $logo      = $this->getAttribute('logo');
        $url       = \htmlspecialchars($this->getAttribute('url'));
        $itemScope = \htmlspecialchars($this->getAttribute('itemscope'));
        $itemType  = 'https://schema.org/Organization';

        if ($this->getAttribute('itemtype') === 'person') {
            $itemType = 'https://schema.org/Person';
        }

        if (\strpos($logo, 'fa-') !== false) {
            $logo = '';
        }

        if (MediaUtils::isMediaUrl($logo)) {
            try {
                $Image = MediaUtils::getImageByUrl($logo);
                $logo  = $Image->getSizeCacheUrl();
            } catch (QUI\Exception $Exception) {
                QUI\System\Log::writeDebugException($Exception);
            }
        }

        $result = '<div itemscope="'.$itemScope.'" itemtype="'.$itemType.'">';
        $result .= '<meta itemprop="name" content="'.$publisher.'">';
        $result .= '<meta itemprop="logo" content="'.$logo.'">';
        $result .= '<meta itemprop="url" content="'.$url.'">';
        $result .= '</div>';

        return $result;
    }
}
