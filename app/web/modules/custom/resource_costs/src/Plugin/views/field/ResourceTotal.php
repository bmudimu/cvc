<?php

namespace Drupal\resource_costs\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to create a pseudo field for totals
 * 
 * @ingroup views_field_handlers
 * 
 * @ViewsField("resource_total")
 */
class ResourceTotal extends FieldPluginBase {

    /**
     * {@inheritdoc}
     */
    public function query() {
        //Leave blank to avoid the field being used in query
    }
    
    /**
     * {@inheritdoc}
     */
    public function render(ResultRow $values) {
        $node = $values->_entity;
        if ($node->bundle() !== 'resource_costs') {
            return '';
        }
        $water = $node->field_water->value;
        $electricity = $node->field_electricity->value;

        $total_costs = $water + $electricity;

        return $total_costs;
    }
}