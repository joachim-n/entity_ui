<?php

namespace Drupal\entity_ui\EntityHandler;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\Routing\Route;

/**
 * Provides an admin UI for entity tabs on target entities with bundles.
 *
 * This expects the entity type to have a bundle entity type, and this bundle
 * entity type to have a 'collection' link template.
 */
class BundleEntityCollection extends EntityUIAdminBase implements EntityUIAdminInterface {

  protected $bundleEntityTypeID;

  protected $bundleEntityType;

  protected $bundleCollectionRouteName;

  /**
   * Constructs a new EntityUIAdminBase.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($entity_type, $entity_type_manager);

    $this->bundleEntityTypeID = $entity_type->getBundleEntityType();
    $this->bundleEntityType = $entity_type_manager->getDefinition($this->bundleEntityTypeID);

    // Whoa!!! MASSIVE assumption! If the bundle entity type overrides
    // or doesn't even use AdminHtmlRouteProvider, then this might not
    // be the route name!
    $this->bundleCollectionRouteName = "entity.{$this->bundleEntityTypeID}.collection";
  }

  /**
   * {@inheritdoc}
   */
  public function getRoutes() {
    $routes = [];

    //$admin_permission = $bundle_entity_type->getAdminPermission();
    // TEMP! TODO!
    $admin_permission = 'made up perm';

    $bundle_collection_link_template = $this->bundleEntityType->getLinkTemplate('collection');

    $route = new Route($bundle_collection_link_template . '/entity_ui');
    $route
      ->addDefaults([
        '_entity_list' => 'entity_tab',
        '_title' => '@label tabs',
        '_title_arguments' => ['@label' => $this->entityType->getLabel()],
      ])
      ->addOptions([
        '_target_entity_type_id' => $this->entityTypeId,
      ])
      ->setRequirement('_permission', $admin_permission);

    $routes["entity_ui.entity_tab.{$this->entityTypeId}.collection"] = $route;

    return $routes;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocalTasks($base_plugin_definition) {
    $tasks = [];

    // Tab for the Entity Tabs admin collection route.
    $task = $base_plugin_definition;
    $task['title'] = 'Entity tabs';
    $task['route_name'] = "entity_ui.entity_tab.{$this->entityTypeId}.collection";
    $task['base_route'] = $this->bundleCollectionRouteName;
    $task['weight'] = 20;

    $tasks[$task['route_name']] = $task;

    // Add a default tab for the type collection.
    // If there is one already, localTasksAlter() will remove it.
    $task = $base_plugin_definition;
    $task['title'] = t('List');
    $task['route_name'] = $this->bundleCollectionRouteName;
    $task['base_route'] = $this->bundleCollectionRouteName;
    $task['weight'] = 0;

    $tasks['entity_ui.' . $this->bundleCollectionRouteName] = $task;

    return $tasks;
  }

  /**
   * {@inheritdoc}
   */
  public function localTasksAlter(&$local_tasks) {
    // Determine whether the bundle entity collection already has a task.
    // We expect this to be the default task, that is, the base route and the
    // route are the same.
    foreach ($local_tasks as $plugin_id => $local_task) {
      if ($local_task['base_route'] == $this->bundleCollectionRouteName &&
          $local_task['route_name'] == $this->bundleCollectionRouteName &&
          $local_task['id'] != 'entity_ui.entity_tabs.local_tasks') {
        // We've found one, so remove the task that we added, as it's surplus.
        unset($local_tasks['entity_ui.entity_tabs.local_tasks:entity_ui.' . $this->bundleCollectionRouteName]);
        // We're done with this entity type. Bail.
        return;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getLocalActions($base_plugin_definition) {
    $actions = [];

    $action = $base_plugin_definition;

    $action = [
      'route_name' => "entity.entity_tab.add_form",
      'route_parameters' => [
        'target_entity_type_id' => $this->entityTypeId,
      ],
      'title' => t('Add entity tab'),
      'appears_on' => array("entity_ui.entity_tab.{$this->entityTypeId}.collection"),
    ];

    $actions["entity_ui.entity_tab.{$this->entityTypeId}.collection.add"] = $action;

    return $actions;
  }

}
