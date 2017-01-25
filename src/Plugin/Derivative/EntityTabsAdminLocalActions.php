<?php

namespace Drupal\entity_ui\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines Entity UI actions on the admin collections.
 */
class EntityTabsAdminLocalActions extends DeriverBase implements ContainerDeriverInterface {

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
      if ($bundle_entity_type_id = $target_entity_type->getBundleEntityType()) {
        $bundle_entity_type = $entity_types[$bundle_entity_type_id];
        if ($bundle_collection_link_template = $bundle_entity_type->getLinkTemplate('collection')) {
          $action = $base_plugin_definition;

          $action = [
            'route_name' => "entity.entity_tab.add_form",
            'route_parameters' => [
              'entity_type_id' => $target_entity_type_id,
            ],
            'title' => t('Add entity tab'),
            'appears_on' => array("entity_ui.entity_tab.{$target_entity_type_id}.collection"),
          ];

          $this->derivatives["entity_ui.entity_tab.{$target_entity_type_id}.collection.add"] = $action;

          continue;
        }
      }

      if ($field_ui_base_route_name = $target_entity_type->get('field_ui_base_route')) {
      }
    }

    return $this->derivatives;
  }

}
