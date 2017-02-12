<?php

namespace Drupal\entity_ui\EntityHandler;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\PreloadableRouteProviderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an admin UI for target entities that use Field UI.
 *
 * This provides admin list of Entity Tabs for target entity types that do not
 * have bundle entities, but do use Field UI.
 */
class BasicFieldUI extends EntityUIAdminBase {

  /**
   * The route provider service.
   *
   * @var \Drupal\Core\Routing\PreloadableRouteProviderInterface
   */
  protected $routeProvider;

  /**
   * The entity type's field UI base route.
   */
  protected $fieldUiBaseRoute;

  /**
   * Constructs a new BasicFieldUI.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Routing\PreloadableRouteProviderInterface $route_provider
   *   The route provider service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityTypeManagerInterface $entity_type_manager,
      PreloadableRouteProviderInterface $route_provider
      ) {
    parent::__construct($entity_type, $entity_type_manager);

    $this->routeProvider = $route_provider;

    $this->fieldUiBaseRouteName = $this->entityType->get('field_ui_base_route');
    $this->fieldUiBaseRoute = $this->routeProvider->getRouteByName($this->fieldUiBaseRouteName);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager'),
      $container->get('router.route_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getRoutes() {
    $routes = [];

    $route = new Route($this->fieldUiBaseRoute->getPath() . '/entity_ui');
    $route
      ->addDefaults([
        '_entity_list' => 'entity_tab',
        '_title' => '@label tabs',
        '_title_arguments' => ['@label' => $this->entityType->getLabel()],
      ])
      ->addOptions([
        '_target_entity_type_id' => $this->entityTypeId,
      ])
      ->setRequirement('_permission', 'administer ' . $this->entityTypeId . ' entity tabs');

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
    $task['base_route'] = $this->fieldUiBaseRouteName;
    $task['weight'] = 20;

    $tasks[$task['route_name']] = $task;

    // We expect that Field UI will also be adding local tasks here, so no need
    // to check that the base route has its own task.

    return $tasks;
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
