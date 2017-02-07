<?php

namespace Drupal\entity_ui\Routing;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Ensures that routes can be provided by entity types.
 */
class AdminRouteProviderSubscriber implements EventSubscriberInterface {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new EntityRouteProviderSubscriber instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Provides routes on route rebuild time.
   *
   * @param \Drupal\Core\Routing\RouteBuildEvent $event
   *   The route build event.
   */
  public function onDynamicRouteEvent(RouteBuildEvent $event) {
    $route_collection = $event->getRouteCollection();

    $entity_types = $this->entityTypeManager->getDefinitions();

    // Get routes from each entity type's entity_ui_admin handler.
    foreach ($entity_types as $target_entity_type_id => $target_entity_type) {
      if ($this->entityTypeManager->hasHandler($target_entity_type_id, 'entity_ui_admin')) {
        $entity_ui_admin_handler = $this->entityTypeManager->getHandler($target_entity_type_id, 'entity_ui_admin');

        // Allow to both return an array of routes or a route collection,
        // like route_callbacks in the routing.yml file.

        $routes = $entity_ui_admin_handler->getRoutes($entity_type);
        if ($routes instanceof RouteCollection) {
          $routes = $routes->all();
        }
        foreach ($routes as $route_name => $route) {
          // Don't override existing routes.
          if (!$route_collection->get($route_name)) {
            $route_collection->add($route_name, $route);
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[RoutingEvents::DYNAMIC][] = ['onDynamicRouteEvent'];
    return $events;
  }

}
