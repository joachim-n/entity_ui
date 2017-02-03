<?php

namespace Drupal\entity_ui\Plugin\EntityTabContent;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\entity_ui\Plugin\EntityTabContentBase;
use Drupal\entity_ui\Plugin\EntityTabContentInterface;

/**
 * @EntityTabContent(
 *   id = "owner_assign",
 *   label = @Translation("Assign entity owner"),
 * )
 */
class OwnerAssign extends EntityTabContentBase implements EntityTabContentInterface {

  /**
   * {@inheritdoc}
   */
  public static function appliesToEntityType(EntityTypeInterface $entity_type) {
    return $entity_type->entityClassImplements(EntityOwnerInterface::class);
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state, $entity_tab) {
    //dsm($entity_tab);
    $form['foo'] = [
      '#markup' => 'owner!',
    ];

    return $form;
  }

  public function buildContent() {
    $build['build'] = [
      '#markup' => 'owner!',
    ];

    return $build;
  }

}
