<?php

namespace Drupal\entity_ui\Plugin\EntityTabContent;

use Drupal\Core\Entity\EntityInterface;
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
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    //dsm($entity_tab);
    $form['foo'] = [
      '#markup' => 'owner!',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildContent(EntityInterface $target_entity) {
    // ok how the fuck do we output a form here???????
    
    $build['build'] = [
      '#markup' => 'owner!',
    ];
    

    return $build;
  }

}
