<?php

/**
 * This file contains package_quiqqer_invoice_ajax_invoices_temporary_product_validatePrice
 */

/**
 * Validate the price and return a validated price
 *
 * @param string|int|float $value
 * @return string
 */
QUI::$Ajax->registerFunction(
    'package_quiqqer_controls_ajax_site_get',
    function ($project, $id, $siteUrl) {
        $Project = QUI::getProjectManager()->decode($project);

        if (!empty($id)) {
            $Site = $Project->get($id);
        } else {
            $Site = QUI\Projects\Site\Utils::getSiteByUrl($Project, $siteUrl);
        }

        return [
            'content' => QUI\Output::getInstance()->parse($Site->getAttribute('content')),
            'title'   => $Site->getAttribute('title'),
            'name'    => $Site->getAttribute('name')
        ];
    },
    ['project', 'id', 'siteUrl'],
    false
);
