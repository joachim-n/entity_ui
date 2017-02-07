<?php

namespace Drupal\entity_ui\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Entity tab entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class AdminRouteProvider extends AdminHtmlRouteProvider {

  /*

  - get list of entity types we provide tabs for
    - content entities
    - has field UI admin route
  - case 1: content entity has a bundle entity:
    - tabify the bundle entity's collection route
  - case 2: does not.
    - tabify the field UI admin route



  */

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    /*
    if ($collection_routes = $this->getPerEntityTypeCollectionRoutes($entity_type)) {
      foreach ($collection_routes as $target_entity_type_id => $collection_route) {
        $collection->add("entity_ui.entity_tab.{$target_entity_type_id}.collection", $collection_route);
      }
    }
    */

    return $collection;
  }

  /**
   * Get the routes for the Entity Tab collections, one for each target type.
   *
   * @param EntityTypeInterface $entity_type
   *  The entity type this route provider is for.
   *
   * @return
   *  An array of routes, keyed by target entity type ID.
   */
  protected function getPerEntityTypeCollectionRoutes(EntityTypeInterface $entity_type) {
    $routes = [];

    // Add an Entity Tab collection route for each viable target entity type.
    $types = $this->entityTypeManager->getDefinitions();
    foreach ($types as $target_entity_type_id => $target_entity_type) {
      // TEMP! TODO!
      $admin_permission = 'made up perm';

      // For entity types that have a bundle entity, try to add the UI
      // collection route alongside the bundle collection route.
      // For example, manage node UI alongside the node types list.
      if ($bundle_entity_type_id = $target_entity_type->getBundleEntityType()) {
        $bundle_entity_type = $types[$bundle_entity_type_id];
        if ($bundle_collection_link_template = $bundle_entity_type->getLinkTemplate('collection')) {
          // Use the admin permission for the bundle, e.g. for node types.
          //$admin_permission = $bundle_entity_type->getAdminPermission();

          $route = new Route($bundle_collection_link_template . '/entity_ui');
          $route
            ->addDefaults([
              '_entity_list' => $entity_type->id(),
              '_title' => '@label tabs',
              '_title_arguments' => ['@label' => $target_entity_type->getLabel()],
            ])
            ->addOptions([
              '_target_entity_type_id' => $target_entity_type_id,
            ])
            ->setRequirement('_permission', $admin_permission);

          $routes[$target_entity_type_id] = $route;

          // Done with this entity type.
          continue;
        }
      }

      // If we've not yet added a route for this target entity type, try to add
      // one alongside the field UI base route.
      if ($field_ui_base_route_name = $target_entity_type->get('field_ui_base_route')) {
        // Use the admin permission for the target entity type.
        //$admin_permission = $target_entity_type->getAdminPermission();

        // TODO: Inject!
        $field_ui_base_route = \Drupal::service('router.route_provider')->getRouteByName($field_ui_base_route_name);
        $route = new Route($field_ui_base_route->getPath() . '/entity_ui');
        $route
          ->addDefaults([
            '_entity_list' => $entity_type->id(),
            '_title' => '@label tabs',
            '_title_arguments' => ['@label' => $target_entity_type->getLabel()],
            '_target_entity_type_id' => $target_entity_type_id,
          ])
          ->setRequirement('_permission', $admin_permission);

        $routes[$target_entity_type_id] = $route;
      }

      // If we're still here, add nothing.
    }

    return $routes;
  }

  /**
   * {@inheritdoc}
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    // Do nothing.
  }

}
