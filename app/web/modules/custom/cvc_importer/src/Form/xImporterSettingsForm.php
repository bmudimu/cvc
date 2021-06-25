<?php
/**
 * @file
 * Contains \Drupal\cvc_importer\Form\xImporterSettingsForm
 */
namespace Drupal\cvc_importer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form to configure Excel Importer module settings
 */
class xImporterSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'cvc_importer_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'cvc_importer.settings'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $types = node_type_get_names();
    $config = $this->config('cvc_importer.settings');

    $form['cvc_importer_introduction'] = array(
      '#type' => 'text_format',
      '#title' => t('Provide introductory text on how to use the form'),
      '#default_value' => $config->get('introduction'),
      '#description' => t('Use this to provide resources such as links to template Excel files.'),
      '#format' => NULL,
      '#weight' => 0,
    );

    $form['cvc_importer_types'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Select the content types to be available'),
      '#default_value' => $config->get('allowed_types'),
      '#options' => $types,
      '#description' => t('Users will be able to use Excel Importer to migrate date into these Content types.'),
      '#weight' => 1,
    );
    $form['array_filter'] = array('#type' => 'value', '#value' => TRUE);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $introduction = $form_state->getValue('cvc_importer_introduction');
    $allowed_types = array_filter($form_state->getValue('cvc_importer_types'));

    // @TODO: Save both value and format
    $this->config('cvc_importer.settings')
      ->set('introduction', $introduction['value'])
      ->save();

    sort($allowed_types);
    $this->config('cvc_importer.settings')
      ->set('allowed_types', $allowed_types)
      ->save();

    parent::submitForm($form, $form_state);
  }
}
