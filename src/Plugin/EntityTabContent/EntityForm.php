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

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    //dsm($entity_tab);
    $form['foo'] = [
      '#markup' => 'form!',
    ];

    return $form;
  }

  public function buildContent(EntityInterface $target_entity) {
    $build['build'] = [
      '#markup' => 'form!',
    ];

    return $build;
  }

}
