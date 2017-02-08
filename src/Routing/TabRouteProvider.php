<?php

namespace Drupal\entity_ui\Routing;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Entity\Routing\EntityRouteProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides ROUTEStabs that the EntityTab entities define for content entities.
 */
class TabRouteProvider implements EntityRouteProviderInterface {
  // EntityHandlerInterface if need DI.

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = new RouteCollection();

    foreach (entity_ui_get_tabs($entity_type) as $tab_id => $entity_tab) {
      // Note that we can't use link templates, because to define them on the
      // target entity type we'd first need to load (and therefore discover)
      // entity tab entities, and this would be circular.
      $path_component = $entity_tab->getPathComponent();
      $path = $entity_type->getLinkTemplate('canonical') . '/' . $path_component;

      $route = new Route($path);
      $route
        ->setDefaults([
          '_controller' => '\Drupal\entity_ui\Controller\EntityTabController::content',
          '_title_callback' => '\Drupal\entity_ui\Controller\EntityTabController::title',
          '_entity_tab_id' => $tab_id,
        ])
        ->setRequirements([
          '_permission' => 'access content', // TODO
        ]);
      // TODO: requirements, options.
      $entity_type_id = $entity_type->id();
      // TODO: namespace with a prefix?
      $collection->add("entity.{$entity_type_id}.{$path_component}", $route);
    }

    return $collection;
  }

}
