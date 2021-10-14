<?php

namespace Drupal\resource_costs\Plugin\views\style;

use Drupal\core\form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render rows as accordions
 * 
 * @ingroup views_style_plugins
 * 
 * @ViewsStyle(
 *   id = "accordion",
 *   title = @Translation("Accordion"),
 *   help = @Translation("Render rows as accordions"),
 *   theme = "views_view_accordion",
 *   display_types = { "normal" }
 * )
 */
class AccordionViewsStyle extends StylePluginBase {
    /**
     * {@inheritdoc}
     */
    protected $usesRowPlugin = True;

    /**
     * Does the style plugin support custom css class for rows.
     * 
     * @var bool
     */
    protected $usesRowClass = True;

    /**
     * Set default options.
     */
    protected function defineOptions() {
        $options = parent::defineOptions();
        $options['summary_text'] = ['default'=> ''];
        return $options;
    }

    /**
     * Style options form
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {
        parent::buildOptionsForm($form, $form_state);
        $form['summary_text'] = [
            '#title' => $this->t('Summary text'),
            '#description' => $this->t('Text to appear in the summary, leave blank if you don\'t want any text to appear.'),
            '#type' => 'textfield',
            '#size' => 30,
            '#default_value' => $this->options['summary_text'],
        ];
    }
}