<?php

namespace Drupal\custom_form_handler\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Entity\EntityInterface;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Create a new node entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "Create a node",
 *   label = @Translation("Create a node"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Creates a new node from Webform Submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class CreateNodeWebformHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */

  // Function to be fired after submitting the Webform.
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    // Get an array of the values from the submission.
    $values = $webform_submission->getData();
    $valuesLength = count($values)/4; //There are 4 values per question on the form

    $evaluation_page = \Drupal::routeMatch()->getParameter('node');
    $evaluation_nid = $evaluation_page->id();
    $evaluation_node = \Drupal::entityTypeManager()->getStorage('node')->load($evaluation_nid);

    $question_query = \Drupal::entityQuery('node')
    ->condition('status', 1)
    ->condition('type', 'evaluation_question')
    ->condition('field_question_category.entity.name', 'Environmental and Management Systems')
    ->sort('title', ASC); //get all the questions from the question content type already created

    $qids = $question_query->execute();

    $question_nodes = Node::loadMultiple($qids);

    foreach ($question_nodes as $question) {
      $questions[] = $question->field_question->value; // put the actual evaluation question text into questions array
    };

    for ($i=0; $i<$valuesLength; $i++) {
      $title = "Question".($i+1);
      $rate = "rate".($i+1);
      $timeline = "time".($i+1);
      $notes = "notes".($i+1);
      $indicator = "indicator".($i+1);
      $qsn = $questions[$i];

      $node_args = [
          'type' => 'environmental_evaluation',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $title,
          'field_parent_node' => $evaluation_nid,
          'field_evaluation_category' => "Environmental Management Systems",
          'field_evaluation_rating' => $values[$rate],
          'field_performance_indicator' => $qsn,
          'field_timeframe'=> $values[$timeline],
          'field_notes' => $values[$notes],
          'field_completion_indicators' => $values[$indicator]
      ];

      $response_node = Node::create($node_args);
      $response_node->enforceIsNew();
      $response_node->save();

      $evaluation_node->field_environmental_evaluation[] = ['target_id'=>$response_node->id()];
      $evaluation_node->save();
    }
  }
}
