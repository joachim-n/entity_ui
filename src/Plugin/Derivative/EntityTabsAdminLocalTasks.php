<?php

namespace Drupal\entity_ui\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds a local task for Entity Tab admin on the target entity type admin UI.
 *
 * For example, this adds a task for Entity Tabs on nodes to the node type admin
 * list.
 */
class EntityTabsAdminLocalTasks extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The entity type manager
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates an SelectionBase object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\Query\QueryInterface $tab_query
   *   The entity query object for entity tab entities.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $entity_types = $this->entityTypeManager->getDefinitions();

    foreach ($entity_types as $target_entity_type_id => $target_entity_type) {
      //
      if ($bundle_entity_type_id = $target_entity_type->getBundleEntityType()) {
        $bundle_entity_type = $entity_types[$bundle_entity_type_id];
        if ($bundle_collection_link_template = $bundle_entity_type->getLinkTemplate('collection')) {
          // Whoa!!! MASSIVE assumption! If the bundle entity type overrides
          // or doesn't even use AdminHtmlRouteProvider, then this might not
          // be the route name!
          $bundle_collection_route_name = "entity.{$bundle_entity_type_id}.collection";

          // Tab for the Entity Tabs admin collection route.
          $task = $base_plugin_definition;
          $task['title'] = 'Entity tabs';
          $task['route_name'] = "entity_ui.entity_tab.{$target_entity_type_id}.collection";
          $task['base_route'] = $bundle_collection_route_name;
          $task['weight'] = 20;

          $this->derivatives[$task['route_name']] = $task;

          // Add a default tab for the type collection, if there isn't one
          // already.
          $tasks = \Drupal::service('plugin.manager.menu.local_task')->getLocalTasksForRoute($bundle_collection_route_name);
          if (empty($tasks[0])) {
            $task = $base_plugin_definition;
            $task['title'] = t('List'); // TODO get title from bundle collection route.
            $task['route_name'] = $bundle_collection_route_name;
            $task['base_route'] = $bundle_collection_route_name;
            $task['weight'] = 0;

            $this->derivatives['entity_ui.' . $bundle_collection_route_name] = $task;
          }
        }
      }
    }

    return $this->derivatives;
  }

}
