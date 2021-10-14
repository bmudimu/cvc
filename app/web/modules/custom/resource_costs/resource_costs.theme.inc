<?php

use Drupal\Core\Template\Attribute;

/**
 * Prepares variables for views unformated rows templates.
 * 
 * Default template: views-view-unformatted.html.twig. 
 * 
 * @param array $variables
 *   An associative array containing:
 *   - view: The view object.
 *   - rows: An array of row items. Each row is an array of content.
 */
function template_preprocess_views_view_accordion(&$variables) {
    $view = $variables['view'];
    $rows = $variables['rows'];
    $style = $view->style_plugin;
    $options = $style->options;
    $variables['default_row_class'] = !empty($options['default_row_class']);
    foreach ($rows as $id => $row) {
        $variables['rows'][$id] =[];
        $variables['rows'][$id]['content'] = $row;
        $variables['rows'][$id]['title'] = $options['summary_text'];
        $variables['rows'][$id]['attributes'] = new Attribute();
        if ($row_class = $view->style_plugin->getRowClass($id)) {
            $variables['rows'][$id]['attributes']->addClass($row_class);
        }
    }
}