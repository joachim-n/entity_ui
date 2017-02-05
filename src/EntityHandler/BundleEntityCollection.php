<?php

namespace Drupal\entity_ui\EntityHandler;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Provides an admin UI for entity tabs on target entities with bundles.
 *
 * This expects the entity type to have a bundle entity type, and this bundle
 * entity type to have a 'collection' link template.
 */
class BundleEntityCollection extends EntityUIAdminBase implements EntityUIAdminInterface {

  /**
   * {@inheritdoc}
   */
  // CAlled from subscriber
  public function getRoutes() {

  }

  // called from derivative
  /**
   * {@inheritdoc}
   */
  public function getLocalTasks() {

  }

  // called from derivative
  /**
   * {@inheritdoc}
   */
  public function getLocalActions($base_plugin_definition) {
    $actions = [];

    $entity_type_id = $this->entityType->id();
    $bundle_entity_type_id = $this->entityType->getBundleEntityType();
    // TODO: inject service.
    $bundle_entity_type = \Drupal::service('entity_type.manager')->getDefinition($bundle_entity_type_id);
    $bundle_collection_link_template = $bundle_entity_type->getLinkTemplate('collection');
    $action = $base_plugin_definition;

    $action = [
      'route_name' => "entity.entity_tab.add_form",
      'route_parameters' => [
        'target_entity_type_id' => $entity_type_id,
      ],
      'title' => t('Add entity tab'),
      'appears_on' => array("entity_ui.entity_tab.{$entity_type_id}.collection"),
    ];

    $actions["entity_ui.entity_tab.{$entity_type_id}.collection.add"] = $action;

    return $actions;
  }

}
