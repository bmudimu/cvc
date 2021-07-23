<?php

namespace Drupal\esg_evaluation\Plugin\EvaluationFormHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Create a new node entity from a webform submission.
 *
 * @EvaluationformHandler(
 *   id = "Create an evaluation node",
 *   label = @Translation("Create  an evaluation node"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Creates a new node from Webform Submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class CreateEvaluationWebformHandler extends WebformHandlerBase {

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
    
  
      $node_args = [
        'type' => 'environmental_evaluation',
        'langcode' => 'en',
        'created' => time(),
        'changed' => time(),
        'uid' => \Drupal::currentUser()->id()),
        'moderation_state' => 'draft',
        'title' => 'ENV Evaluation 1',
        'field_evaluation_category' => "environmental",
        'field_evaluation_rating' => $values['rate1'],
        'field_performance_indicator' => $values['env1'],
        'field_timeframe',=> $values['time1'],
        'field_notes' => $values['notes1'],
        'field_compliance_indicators' => $values['indicator1'],
        'body' => [
          'value' => $values['env2'],
          'format' => 'full_html'
        ]
      ];

        $node = Node::create($node_args);
        $node->save();
    }
    }

  