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
 * TODO
 */
class TabRouteProvider implements EntityRouteProviderInterface {
  // EntityHandlerInterface if need DI.
  
  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = new RouteCollection();
    
    foreach (entity_ui_get_ops() as $operation) {
      $path = $entity_type->getLinkTemplate($operation);
      $route = new Route($path);
      $route
        ->setDefaults([
          '_controller' => "entity_ui_output",
          '_title' => $operation, // TODO
        ])
        ->setRequirements([
          '_permission' => 'access content', // TODO
        ]);
      // TODO: requirements, options.
      $entity_type_id = $entity_type->id();
      $collection->add("entity.{$entity_type_id}.{$operation}", $route);
      //print_r("entity.{$entity_type_id}.{$operation}\n");
    }
    
    /*
    // TODO: need link template first!
    //$path = $entity_type->getLinkTemplate('moderation-form');
    $path = 'entity/foobar';
    $route = new Route($path);
    $route
      ->setDefaults([
        '_entity_form' => "{$entity_type_id}.moderation",
        '_title' => 'Moderation',
      ])
      ->setRequirement('_permission', 'administer moderation states') // @todo Come up with a new permission.
      ->setOption('parameters', [
        $entity_type_id => ['type' => 'entity:' . $entity_type_id],
      ]);
    $collection->add("entity.{$entity_type_id}.moderation", $moderation_route);
    */

    /*
    if ($moderation_route = $this->getModerationFormRoute($entity_type)) {
      $entity_type_id = $entity_type->id();
      $collection->add("entity.{$entity_type_id}.OPERATION", $moderation_route);
    }
    */

    return $collection;
  }
  
}
