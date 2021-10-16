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
 *   id = "cvc_handler",
 *   label = @Translation("Create nodes from webforms"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Creates a new node from Webform Submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class BedsWebformHandler extends WebformHandlerBase {

    /**
     * {@inheritdoc}
     */
  
    // Function to be fired after submitting the Webform.
    public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
      // Get an array of the values from the submission.
      $values = $webform_submission->getData();
      
      // $articles = \Drupal::entityQuery('node')->condition('type', 'article')->execute();
      // $nodes = \Drupal\node\Entity\Node::loadMultiple($articles);
      // $variables['name'] = $nodes;
      // one

  $year = Term::load($values['evaluation_year']);
  $quarter = Term::load($values['evaluation_qtr']);
  $node = Node::load($values['client_name']);
  $title = $node->get('title')->value;
    
      $beds_args = [
        'type' => 'beds',
        'langcode' => 'en',
        'created' => time(),
        'changed' => time(),
        'uid' => \Drupal::currentUser()->id(),
        'moderation_state' => 'draft',
        'title' => $title.' Bed Count '.$year->getName().' '.$quarter->getName(),
        'field_client_name' => $values['client_name'],
        'field_year' => $values['evaluation_year'],
        'field_quarter' => $values['evaluation_qtr'],
        'field_total_beds' => $values['total_beds'],
        'field_operational_beds' => $values['operational_beds'],
        'field_notes' => $values['stats1_comments']
      ];

        $node = Node::create($beds_args);
        $node->save();
    }
    }

  