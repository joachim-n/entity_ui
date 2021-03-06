<?php

namespace Drupal\entity_ui\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\entity_ui\Entity\EntityTabInterface;

/**
 * Manages Entity tab content plugins.
 *
 * @see \Drupal\entity_ui\Plugin\EntityTabContentInterface
 */
class EntityTabContentManager extends DefaultPluginManager {

  /**
   * Constructor for EntityTabContentManager objects.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/EntityTabContent', $namespaces, $module_handler, 'Drupal\entity_ui\Plugin\EntityTabContentInterface', 'Drupal\entity_ui\Annotation\EntityTabContent');

    $this->alterInfo('entity_ui_entity_tab_content_info');
    $this->setCacheBackend($cache_backend, 'entity_ui_entity_tab_content_plugins');
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = array()) {
    // Sanity check: we expect the Entity Tab entity this plugin is on to be in
    // the configuration array.
    // TODO: ask someone with more understanding of the innards of plugins about
    // whether this is wrong.
    if (!isset($configuration['entity_tab']) || !($configuration['entity_tab'] instanceof EntityTabInterface)) {
      throw new \Exception("Entity tab plugin configuration must contain an Entity Tab entity.");
    }

    return parent::createInstance($plugin_id, $configuration);
  }

}
