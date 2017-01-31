<?php

namespace Drupal\entity_ui\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\entity_ui\Plugin\EntityTabContentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides TODO.
 */
class EntityTabController implements ContainerInjectionInterface {

  /**
   * The entity type manager
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The currently active route match object.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRouteMatch;

  /**
   * The Entity Tab content plugin manager
   *
   * @var \Drupal\entity_ui\Plugin\EntityTabContentManager
   */
  protected $entityTabContentPluginManager;

  /**
   * Constructs a new TODO object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\entity_ui\Plugin\EntityTabContentManager
   *   The entity tab plugin manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $current_route_match
   *   The currently active route match object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityTabContentManager $entity_tab_content_manager, RouteMatchInterface $current_route_match) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTabContentPluginManager = $entity_tab_content_manager;
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.entity_tab_content.processor'),
      $container->get('current_route_match')
    );
  }

  /**
   * Builds the content for the entity tab.
   *
   * @return
   *  A render array.
   */
  public function content() {
    // Get the entity tab ID from the current route.
    // @todo is it possible to set this as a parameter and have it upcasted?
    $entity_tab_id = $this->currentRouteMatch->getRouteObject()->getDefault('_content_plugin');
    $entity_tab = \Drupal::service('entity_type.manager')->getStorage('entity_tab')->load($entity_tab_id);

    $content_plugin = $this->entityTabContentPluginManager->createInstance($entity_tab->getPluginID(), []);

    return $content_plugin->buildContent();
  }

  public function title() {
    return 'title!';
  }

  // TODO: appliesToEntityTypeAndBundle.

  // TODO: access

}