<?php

namespace Drupal\entity_ui\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Entity UI tabs on target entities.
 */
class EntityLocalTasks extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The entity type manager
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity query object for entity tabs.
   *
   * @var \Drupal\Core\Entity\Query\QueryInterface
   */
  protected $tabQuery;

  /**
   * Creates an SelectionBase object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\Query\QueryInterface $tab_query
   *   The entity query object for entity tab entities.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, QueryInterface $tab_query) {
    $this->entityTypeManager = $entity_type_manager;
    $this->tabQuery = $tab_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity.query')->get('entity_tab')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type_id => $entity_type_definition) {
      foreach (entity_ui_get_ops() as $operation) {
        $task = $base_plugin_definition;
        $task['title'] = $operation;
        $task['route_name'] = "entity.{$entity_type_id}.{$operation}";
        $task['base_route'] = "entity.{$entity_type_id}.canonical";

        $this->derivatives["entity.{$entity_type_id}.{$operation}"] = $task;
      }
    }

    return $this->derivatives;
  }

}
