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
 *   id = "evaluation_score_handler",
 *   label = @Translation("Create Evaluation"),
 *   category = @Translation("Entity Creation"),
 *   description = @Translation("Create evaluation node."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */

class EvaluationWebformHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    // Get an array of the values from the submission.
    $values = $webform_submission->getData();
    $elements = WebformFormHelper::flattenElements($form);
    $form_id = $webform_submission->getWebform()->id();
    $valuesLength = (count($values) - 3)/2; //There are 2 elements in the submission per question on the form and 3 info based values

    // $evaluation_page = \Drupal::routeMatch()->getParameter('node'); // get the route of the node from which form is being submited
    // $evaluation_nid = $evaluation_page->id();
    // $evaluation_node = \Drupal::entityTypeManager()->getStorage('node')->load($evaluation_nid);

    $year = Term::load($values['evaluation_year']);
    $quarter = Term::load($values['evaluation_qtr']);
    $node = Node::load($values['client_name']); // get the client node to get actual title text
    $title = $node->get('title')->value;

    $category_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
            ->loadByProperties(['name' => 'Environmental', 'vid' => 'evaluation_categories']);
    $category_term = reset($category_term);

    $eval_title = $title."-".$year->getName()."-".ucfirst($form_id).' Evaluation';

    $evaluation_args = [
      'type' => 'evaluation',
      'langcode' => 'en',
      'created' => time(),
      'changed' => time(),
      'uid' => \Drupal::currentUser()->id(),
      'moderation_state' => 'draft',
      'title' => $eval_title,
      'field_year' => $year ,
      'field_quarter' => $quarter,
      'field_client_name' => $values['client_name'],
      'field_evaluation_category' => $category_term->id()
    ];

    $eval_node = Node::create($evaluation_args);
    $eval_node->enforceIsNew();
    $eval_node->save();

    for ($i = 1; $i <= $valuesLength; $i++) {
        preg_match('#</span>(.*?)</div#s', trim($elements['qn'.$i]['#markup']), $match); //strip html using regex 
        $question = $match;
        if (empty($match)) {
          $question[0] = '';
          $question[1] = '';
        } //find better solution for capturing errors incase no match was found

        // Check if the node item already exists getting an array of Node IDs - nids.
        $response_title = $title."-".$year->getName()."-".ucfirst($form_id).' Q'.$i;
        $res_query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
        $res_query->condition('title', $response_title);
        $response_nids = $res_query->execute();

        $scoreitem_args = [
          'type' => 'evaluation_score',
          'langcode' => 'en',
          'created' => time(),
          'changed' => time(),
          'uid' => \Drupal::currentUser()->id(),
          'moderation_state' => 'draft',
          'title' => $response_title,
          'field_evaluation_item_no' => $i,
          'field_question' => $question[1] ,
          'field_evaluation_score' => $values['rating'.$i],
          'field_notes' => $values['notes'.$i]
        ];

        if (empty($response_nids)) {
          $response_node = Node::create($scoreitem_args);
          $response_node->enforceIsNew();
          $response_node->save();

          $eval_node->field_evaluation_item[] = ['target_id'=>$response_node->id()];
          $eval_node->save();

        } else {
          $first_key = array_key_first($response_nids);
          $response_exists = Node::load($response_nids[$first_key]);
          $response_exists->set('field_question', $question[1]);
          $response_exists->set('field_evaluation_score', $values['rating'.$i]);
          $response_exists->set('field_notes', $values['notes'.$i]);
          $response_exists->set('changed', time());
          $response_exists->save();

          //no need to re-link response to the evaluation since its assumed to be already existing

        }
      }



  }
}

  