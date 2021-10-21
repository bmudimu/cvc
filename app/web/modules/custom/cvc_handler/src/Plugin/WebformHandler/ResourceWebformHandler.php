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
 *   id = "cvc_resource_handler",
 *   label = @Translation("Create resource costs nodes"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Create resource costs nodes"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class ResourceWebformHandler extends WebformHandlerBase {

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
      $months = [53, 54, 55, 56, 57, 58, 59, 60, 62, 63, 64, 65]; //month taxonomy key
      $x = (int)($quarter->getName()[-1]);
      $m = 3*$x;

      for ($i = $m-2 ; $i <= $m; $i++){
        $water = $values['water'.$i];
        $power = $values['electricity'.$i];
        $notes = $values['notes1'];
        $month = $months[$i-1];

        // Check if the node item already exists getting an array of Node IDs - nids.
        $title = $client_title.' Resource Costs '.$year->getName().' '.$quarter->getName().' Month '.$i;
        $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
        $query->condition('title', $title);
        $nids = $query->execute();
        
        $resource_args = [
          'type' => 'resource_costs',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $client_title.' Resource Costs '.$year->getName().' '.$quarter->getName().' Month '.$i,
          'field_client_name' => $values['client_name'],
          'field_year' => $values['year'],
          'field_quarter' => $values['quarter'],
          'field_month' => $month,
          'field_water' => $water,
          'field_electricity' => $power,
          'field_notes' => $notes
        ];

        
        if (!(empty($nids))) {

          $first_key = array_key_first($nids);
          $node = Node::load($nids[$first_key]);

          $node->set('field_water', $water);
          $node->set('field_electricity', $power);
          $node->set('field_notes', $notes);
          $node->set('changed', time());
          $node->save();
        
        } else {
        
           // Build and save the node item.
           $node = Node::create($resource_args);
           $node->save();
        
          }
        }
      }
    }

