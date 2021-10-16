<?php

namespace Drupal\webform_handler\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformInterface;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Webform example handler.
 *
 * @WebformHandler(
 *   id = "example",
 *   label = @Translation("Example"),
 *   category = @Translation("Example"),
 *   description = @Translation("Example of a webform submission handler."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_IGNORED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class CvcWebformHandler extends WebformHandlerBase {
    /**
     * {@inheritdoc}
     */
  
    // Function to be fired after submitting the Webform.
    public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
      // Get an array of the values from the submission.
      $values = $webform_submission->getData();
          
      $beds_args = [
        'type' => 'beds',
        'langcode' => 'en',
        'created' => time(),
        'changed' => time(),
        'uid' => \Drupal::currentUser()->id(),
        'moderation_state' => 'draft',
        'title' => $values['client_name'].' Beds Count '.$values['year'].' '.$values['quarter'],
        'field_client' => $values['client_name'],
        'field_year' => $values['year'],
        'field_quarter' => $values['quarter'],
        'field_total_beds' => $values['total_beds'],
        'field_operational_beds' => $values['operational_beds'],
        'field_notes' => $values['comments1']
      ];

        $node = Node::create($beds_args);
        $node->save();
    }
}

