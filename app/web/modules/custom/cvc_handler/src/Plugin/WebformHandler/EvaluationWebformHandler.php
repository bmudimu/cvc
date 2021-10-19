<?php

namespace Drupal\cvc_handler\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\Utility\WebformFormHelper;
use Drupal\taxonomy\Entity\Term;

/**
 * Create a new node entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "social_evaluation_handler",
 *   label = @Translation("Social Evaluation"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Creates a new social evaluation node."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class EvaluationWebformHandler extends WebformHandlerBase {

    // /**
    //  * {@inheritdoc}
    //  */
  
    // // Function to be fired after submitting the Webform.
    // public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    //   // Get an array of the values from the submission.
    //   $values = $webform_submission->getData();
    //   // $all_values = $webform_submission->getCompleteForm();
      
    //   // $articles = \Drupal::entityQuery('node')->condition('type', 'article')->execute();
    //   // $nodes = \Drupal\node\Entity\Node::loadMultiple($articles);
    //   // $variables['name'] = $nodes;
    //   // one

    //   $year = Term::load($values['evaluation_year_']);
    //   // $quarter = Term::load($values['evaluation_qtr']);
    //   $node = Node::load($values['client_name']);
    //   $title = $node->get('title')->value;

    //   $num_fields = count($values);

    //   for ($i = 2; $i <= 2; $i++) {
    //       $evaluation_args = [
    //         'type' => 'evaluation_score',
    //         'langcode' => 'en',
    //         'created' => time(),
    //         'changed' => time(),
    //         'uid' => \Drupal::currentUser()->id(),
    //         'moderation_state' => 'draft',
    //         'title' => $title.' Question '.$i. ' Score '.$year->getName(),
    //         'field_question' => $values['qn'.$i]['#markup'],
    //         'field_evaluation_score' => $values['rating'.$i],
    //         'field_notes' => $values['qn'.$i] //$values['notes'.$i]
    //       ];

    //         $node = Node::create($evaluation_args);
    //         $node->save();
    //     }
    // }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    // Get an array of the values from the submission.
    $values = $webform_submission->getData();
    $elements = WebformFormHelper::flattenElements($form);

    $year = Term::load($values['evaluation_year']);
    $quarter = Term::load($values['evaluation_qtr']);
    $node = Node::load($values['client_name']);
    $title = $node->get('title')->value;

    for ($i = 1; $i <= 30; $i++) {
        preg_match('#</span>(.*?)</div#s', trim($elements['qn'.$i]['#markup']), $match);
        $question = $match;
        if (empty($match)) {
          $question[0] = '';
          $question[1] = '';
        }
        $evaluation_args = [
          'type' => 'evaluation_score',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $title.' Question '.$i. ' Score '.$year->getName(),
          'field_question' => $question[1] ,
          'field_evaluation_score' => $values['rating'.$i],
          'field_notes' => $values['notes'.$i]
        ];

          $node = Node::create($evaluation_args);
          $node->save();
      }
  }
}

  