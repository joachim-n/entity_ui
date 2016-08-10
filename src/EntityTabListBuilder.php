<?php

namespace Drupal\entity_ui;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Entity tab entities.
 
 TODO: Crib from EntityDisplayModeListBuilder
 */
class EntityTabListBuilder extends GroupedConfigEntityListBuilder {
  
  
  /**
   * Constructs a new EntityTabListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Entity\EntityTypeInterface[] $entity_types
   *   List of all entity types.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, array $entity_types) {
    parent::__construct($entity_type, $storage);

    $this->entityTypes = $entity_types;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_type,
      $entity_manager->getStorage($entity_type->id()),
      $entity_manager->getDefinitions()
    );
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getEntityGroup($entity) {
    return 'foo';
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getGroups() {
    $groups = [];
    foreach ($this->entityTypes as $entity_type_id => $entity_type) {
      if ($entity_type->getGroup() == 'content') {
        $groups[$entity_type_id] = $entity_type->getLabel();
      }
    }
    return $groups;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Entity tab');
    $header['id'] = $this->t('Machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getGroupedAddURL($grouping) {
    return Url::fromRoute('entity.entity_view_mode.add_form', [
      'entity_type_id' => $grouping,
    ]);
  }

}
