<?php

namespace Drupal\esg_progress\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Class EsgRangeBlock
 * 
 * @Block(
 *   id = "esg_range_block",
 *   admin_label = @Translation("Evaluation Score"),
 *   category = @Translation("Custom")
 * )
 */

 class EsgRangeBlock extends BlockBase
 {
     /**
      * {@inheritdoc}
      */
      public function build()
      {
          $nid = \Drupal::routeMatch()->getParameter('node')->id();
          $evaluation_node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

          $year = $evaluation_node->get('field_year')->entity->name->value;
          $quarter = $evaluation_node->get('field_quarter')->entity->name->value;
        //   $client = $evaluation_node->get('field_client_name')->get(0)->value;
          $nids = $evaluation_node->field_environmental_evaluation->getValue();

          $evaluations = array();
          foreach ($nids as $evaluation) {
              $evaluation_id = Node::load($evaluation['target_id']);
              $evaluation_scores[] = $evaluation_id->get('field_evaluation_rating')->value;
          }
          $evaluation_score = array_sum($evaluation_scores);
          $renderable = [
            '#theme' => 'esgrange',
            '#year' => $year,
            '#quarter' => $quarter,
            // '#client' => $client,
            '#score' => $evaluation_score,
          ];

          return $renderable;
      }
 }
