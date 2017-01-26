<?php

namespace Drupal\entity_ui\Plugin\EntityTabContent;

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
  
  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['foo'] = [
      '#markup' => 'form!',
    ];
    
    return $form;
  }

}
