<?php

namespace Drupal\entity_ui\Plugin\EntityTabContent;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_ui\Plugin\EntityTabContentBase;
use Drupal\entity_ui\Plugin\EntityTabContentInterface;

/**
 * @EntityTabContent(
 *   id = "entity_view",
 *   label = @Translation("Entity view"),
 * )
 */
class EntityView extends EntityTabContentBase implements EntityTabContentInterface {

  protected $defaults = [
    'view_mode' => 'default',
  ];

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // TODO: inject.
    $view_mode_options = \Drupal::service('entity_display.repository')->getViewModeOptions($this->targetEntityTypeId);
    $form['view_mode'] = [
      '#type' => 'select',
      '#title' => t('View mode'),
      '#description' => t("The view mode in which to display the entity."),
      '#options' => $view_mode_options,
      '#default_value' => $this->configuration['view_mode'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildContent(EntityInterface $target_entity) {
    $view_builder = \Drupal::service('entity_type.manager')->getViewBuilder($this->targetEntityTypeId);

    return $view_builder->view($target_entity, $this->configuration['view_mode']);
  }

}
