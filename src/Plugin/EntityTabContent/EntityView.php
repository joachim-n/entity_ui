<?php

namespace Drupal\entity_ui\Plugin\EntityTabContent;

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
    ];

    return $form;
  }

  public function buildContent() {
    $build['build'] = [
      '#markup' => 'view!',
    ];

    return $build;
  }

}
