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
    $form['foo'] = [
      '#markup' => 'view!',
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
