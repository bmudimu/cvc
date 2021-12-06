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
          $evaluation_node = \Drupal::entityTypeManager()->getStorage('node')->load($nid); // get current node

          // $year = $evaluation_node->get('field_year')->entity->name->value;
          // $quarter = $evaluation_node->get('field_quarter')->entity->name->value;
          // $client = $evaluation_node->get('field_client_name')->get(0)->value;
          $nids = $evaluation_node->field_evaluation_item->getValue();

          $evaluations = array();

          foreach ($nids as $evaluation) {
              $score_node = \Drupal::entityTypeManager()->getStorage('node')->load($evaluation['target_id']);
              $evaluations[] = $score_node->get('field_evaluation_score')->value;
          }
          $eval_scores = array_replace($evaluations,array_fill_keys(array_keys($evaluations, null),0));
          $score_tally = array_count_values($eval_scores);

          for ($c = 0; $c <= 4; $c++) {
            if (array_key_exists($c, $score_tally)) {
              $rating_count[$c] = $score_tally[$c];
              $rating_score[$c] = (int)($rating_count[$c])*$c;
            } else {
              $rating_count[$c] = 0;
              $rating_score[$c] = 0;
            }

          };

          $total_count = array_sum($rating_count);
          $evals_count = $nid;
          $total_score = array_sum($rating_score);

          if ($total_count == 0){
            $evaluation_score = 0;
            $contribution = 0;
          } else {
            $evaluation_score = number_format($total_score / ($total_count * 4)*100, 1);
          };
          
          $tbl_data = [
            'r1' => ['Very Unsatisfied', $rating_count[0], 0, $total_count == 0 ? 0.0 : number_format($rating_count[0]/$total_count*100, 1) ],
            'r2' => ['Unsatisfied', $rating_count[1], $rating_score[1], $total_count == 0 ? 0.0 : number_format($rating_count[1]/$total_count*100, 1) ],
            'r3' => ['Neutral', $rating_count[2], $rating_score[2], $total_count == 0 ? 0.0 : number_format($rating_count[2]/$total_count*100, 1) ],
            'r4' => ['Satisfied', $rating_count[3], $rating_score[3], $total_count == 0 ? 0.0 : number_format($rating_count[3]/$total_count*100, 1) ],
            'r5' => ['Very Satisfied',$rating_count[4], $rating_score[4], $total_count == 0 ? 0.0 : number_format($rating_count[4]/$total_count*100, 1) ],
          ];

          $renderable = [
            '#theme' => 'scoretable',
            '#data' => $tbl_data,
            '#total_count' => $total_count,
            '#total_score' => $total_score,
            '#evaluation_score' => $evaluation_score,
          ];

          return $renderable;
      }

      /**
       * {@inheritdoc}
       */
      public function getCacheMaxAge(){
        return 0;
      }
 }