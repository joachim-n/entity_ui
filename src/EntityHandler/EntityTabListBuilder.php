<?php

namespace Drupal\entity_ui\EntityHandler;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Entity tab entities. TODO
 */
class EntityTabListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function render() {
    dsm("list builder!");
    return [];
  }

}
