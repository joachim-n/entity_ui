<?php

namespace Drupal\entity_ui\Plugin\EntityTabContent;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_ui\Plugin\EntityTabContentBase;
use Drupal\entity_ui\Plugin\EntityTabContentInterface;

/**
 * @EntityTabContent(
 *   id = "entity_form",
 *   label = @Translation("Entity form"),
 * )
 */
class EntityForm extends EntityTabContentBase implements EntityTabContentInterface {

  protected $defaults = [
    'form_mode' => 'default',
  ];

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // TODO: inject.
    $form_mode_options = \Drupal::service('entity_display.repository')->getFormModeOptions($this->targetEntityTypeId);
    $form['form_mode'] = [
      '#type' => 'select',
      '#title' => t('Form mode'),
      '#description' => t("The form mode to display."),
      '#options' => $form_mode_options,
      '#default_value' => $this->configuration['form_mode'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildContent(EntityInterface $target_entity) {
    $build['build'] = [
      '#markup' => 'form!',
    ];

    return $build;
  }

}
