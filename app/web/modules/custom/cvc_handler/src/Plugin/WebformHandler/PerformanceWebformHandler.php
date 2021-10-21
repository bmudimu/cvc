<?php

namespace Drupal\cvc_handler\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Create a new node entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "cvc_performance_handler",
 *   label = @Translation("Create performance stats nodes"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Create performance stats nodes"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class PerformanceWebformHandler extends WebformHandlerBase {

    /**
     * {@inheritdoc}
     */
  
    // Function to be fired after submitting the Webform.
    public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
      // Get an array of the values from the submission.
      $values = $webform_submission->getData();

      $year = Term::load($values['year']);
      $quarter = Term::load($values['quarter']);
      $client_node = Node::load($values['client_name']);
      $client_title = $client_node->get('title')->value;


        // Check if the node item already exists getting an array of Node IDs - nids.
        // $title = $client_title.' Resource Costs '.$year->getName().' '.$quarter->getName().' Month '.$i;
        // $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
        // $query->condition('title', $title);
        // $nids = $query->execute();
        
        $wages_args = [
          'type' => 'wages',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $client_title.' Wages '.$year->getName().' '.$quarter->getName(),
          'field_client_name' => $values['client_name'],
          'field_year' => $values['year'],
          'field_quarter' => $values['quarter'],
          'field_minimum_wage' => $values['minimum_wage'],
          'field_average_wage' => $values['average_wage'],
          'field_notes' => $values['notes1']
        ];

        // Build and save the node item.
        $node = Node::create($wages_args);
        $node->save();

        $tax_args = [
          'type' => 'tax_contribution',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $client_title.' Tax Contribution '.$year->getName().' '.$quarter->getName(),
          'field_client_name' => $values['client_name'],
          'field_year' => $values['year'],
          'field_quarter' => $values['quarter'],
          'field_paye' => $values['paye'],
          'field_vat' => $values['vat'],
          'field_uif' => $values['uif'],
          'field_sdl' => $values['sdl'],
          'field_notes' => $values['notes2']
        ];

        // Build and save the node item.
        $node = Node::create($tax_args);
        $node->save();

        $inhouse_args = [
          'type' => 'inhouse_training',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $client_title.' Inhouse Training '.$year->getName().' '.$quarter->getName(),
          'field_client_name' => $values['client_name'],
          'field_year' => $values['year'],
          'field_quarter' => $values['quarter'],
          'field_training_monthly_spend' => $values['training_spend_mon'],
          'field_trained_male' => $values['trained_male'],
          'field_trained_female' => $values['trained_female'],
          'field_training_level' => $values['training_level'],
          'field_days_for_training' => $values['training_days'],
          'field_notes' => $values['notes3']
        ];

        // Build and save the node item.
        $node = Node::create($inhouse_args);
        $node->save();

        $smme_args = [
          'type' => 'smme_training',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $client_title.' SMME Training '.$year->getName().' '.$quarter->getName(),
          'field_client_name' => $values['client_name'],
          'field_year' => $values['year'],
          'field_quarter' => $values['quarter'],
          'field_smme_trained' => $values['trained_smme'],
          'field_local_smme_trained' => $values['trained_local_smme'],
          'field_bbbee_trained' => $values['trained_bbbee_smme'],
          'field_number_of_contracts' => $values['contracts_awarded'],
          'field_training_spend_per_year' => $values['training_spend_yr'],
          'field_wo_bbbee_trained' => $values['trained_wo_bbbee'],
          'field_notes' => $values['notes4']
        ];

        // Build and save the node item.
        $node = Node::create($smme_args);
        $node->save();

        $injuries_args = [
          'type' => 'injuries_and_fatalities',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $client_title.' Injuries '.$year->getName().' '.$quarter->getName(),
          'field_client_name' => $values['client_name'],
          'field_year' => $values['year'],
          'field_quarter' => $values['quarter'],
          'field_number_of_trained_people' => $values['trained_health_safety'],
          'field_number_of_injuries' => $values['injuries_fatalities'],
          'field_number_of_claims' => $values['claims'],
          'field_percent_declined_claims' => $values['declined_claims'],
          'field_lost_time' => $values['lost_time'],
          'field_notes' => $values['notes5']
        ];

        // Build and save the node item.
        $node = Node::create($injuries_args);
        $node->save();

        $csr_args = [
          'type' => 'csr_spend',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $client_title.' CSR Spend '.$year->getName().' '.$quarter->getName(),
          'field_client_name' => $values['client_name'],
          'field_year' => $values['year'],
          'field_quarter' => $values['quarter'],
          'field_csr_spend' => $values['csr_spend'],
          'field_percent_to_turnover' => $values['percent_to_turnover'],
          'field_percent_to_profit' => $values['percent_to_profit'],
          'field_sector' => $values['sector'],
          'field_notes' => $values['notes6']
        ];
        
           // Build and save the node item.
           $node = Node::create($csr_args);
           $node->save();
      }
    }

