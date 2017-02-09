<?php

namespace Drupal\entity_ui\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\LocalTaskManagerInterface;
use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\entity_ui\Plugin\EntityTabContentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for editing and creating entity tab entities.
 */
class EntityTabForm extends EntityForm {

  /**
   * The Entity Tab content plugin manager
   *
   * @var \Drupal\entity_ui\Plugin\EntityTabContentManager
   */
  protected $entityTabContentPluginManager;

  /**
   * The menu local task plugin manager.
   *
   * @var \Drupal\Core\Menu\LocalTaskManagerInterface
   */
  protected $menuLocalTaskPluginManager;

  /**
   * The router builder service.
   *
   * @var \Drupal\Core\Routing\RouteBuilderInterface
   */
  protected $routerBuilder;

  /**
   * Constructs a new EntityTabForm.
   *
   * @param \Drupal\entity_ui\Plugin\EntityTabContentManager
   *   The entity tab plugin manager.
   */
  public function __construct(EntityTabContentManager $entity_tab_content_manager,
      LocalTaskManagerInterface $plugin_manager_menu_local_task,
      RouteBuilderInterface $router_builder) {
    $this->entityTabContentPluginManager = $entity_tab_content_manager;
    $this->menuLocalTaskPluginManager = $plugin_manager_menu_local_task;
    $this->routerBuilder = $router_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.entity_tab_content.processor'),
      $container->get('plugin.manager.menu.local_task'),
      $container->get('router.builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function init(FormStateInterface $form_state) {
    parent::init($form_state);
    $this->entityType = $this->entityManager->getDefinition($this->entity->getEntityTypeId());
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityFromRouteMatch(RouteMatchInterface $route_match, $entity_type_id) {
    if ($route_match->getRawParameter($entity_type_id) !== NULL) {
      $entity = $route_match->getParameter($entity_type_id);
    }
    else {
      $values = [];

      // Get the target entity type from the route's parameter.
      $target_entity_type_id = $route_match->getParameter('target_entity_type_id');
      $values['target_entity_type'] = $target_entity_type_id;

      $entity = $this->entityTypeManager->getStorage($entity_type_id)->create($values);
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $target_entity_type_id = NULL) {
    $form = parent::buildForm($form, $form_state);

    if ($this->entity->isNew()) {
      if (empty($target_entity_type_id)) {
        // We can't operate without our additional parameter.
        throw new \Exception('Missing parameter $target_entity_type_id.');
      }

      $target_entity_type = $this->entityManager->getDefinition($this->entity->getTargetEntityTypeID());
      $form['#title'] = $this->t('Add new %label @entity-type', array(
        '%label' => $target_entity_type->getLabel(),
        '@entity-type' => $this->entityType->getLowercaseLabel(),
      ));
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $entity_tab = $this->entity;
    dsm($entity_tab);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity_tab->label(),
      '#description' => $this->t("The admin label for the Entity tab."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity_tab->id(),
      '#field_prefix' => $entity_tab->isNew() ? $entity_tab->getTargetEntityTypeID() . '.' : '',
      '#machine_name' => [
        'exists' => [$this, 'exists'],
        'replace_pattern' => '[^a-z0-9_.]+',
      ],
      '#disabled' => !$entity_tab->isNew(),
    ];

    $form['tab_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tab title'),
      '#maxlength' => 255,
      '#default_value' => $entity_tab->get('tab_title'),
      '#description' => $this->t("The label for the tab on the target entity type."),
      '#required' => TRUE,
    ];

    $form['page_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Page title'),
      '#maxlength' => 255,
      '#default_value' => $entity_tab->get('page_title'),
      '#description' => $this->t("The page title to show when the entity tab is displayed."),
      '#required' => TRUE,
    ];

    $targetEntityTypeID = $entity_tab->getTargetEntityTypeID();
    $targetEntityType = $this->entityManager->getDefinition($targetEntityTypeID);
    $targetEntityTypeCanonicalURL = $targetEntityType->getLinkTemplate('canonical');
    $example_url = str_replace("{{$targetEntityTypeID}}", 'ID', $targetEntityTypeCanonicalURL);
    $form['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path component'),
      '#field_prefix' => $example_url . '/',
      '#maxlength' => 16,
      '#size' => 16,
      '#default_value' => $entity_tab->get('path'),
      '#description' => $this->t("The path component to append to the entity's canonical URL to form the URL for this tab."),
      '#required' => TRUE,
    ];

    /*
    $form['verb'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Permission verb'),
      '#maxlength' => 16,
      '#size' => 16,
      '#default_value' => $entity_tab->get('verb'),
      '#description' => $this->t("TODO."),
      '#required' => TRUE,
    ];
    */

    $form['content'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Content options'),
      '#description' => $this->t('The output to show on this tab.'),
      '#tree' => FALSE,
      '#prefix' => '<div id="content-settings-wrapper">',
      '#suffix' => '</div>',
    ];

    $options = [];
    foreach ($this->entityTabContentPluginManager->getDefinitions() as $plugin_id => $definition) {
      if ($definition['class']::appliesToEntityType($targetEntityType)) {
        $options[$plugin_id] = $definition['label'];
      }
    }
    natcasesort($options);
    $form['content']['content_plugin'] = [
      '#type' => 'select',
      '#title' => $this->t('Content'),
      '#options' => $options,
      '#default_value' => $entity_tab->get('content_plugin'),
      '#description' => $this->t("The content provider for this tab."),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateSelectedPluginType',
        'wrapper' => 'content-settings-wrapper',
        'event' => 'change',
        'method' => 'replace',
      ],
    ];

    $form['content']['content_plugin_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
      '#submit' => ['::submitSelectPlugin'],
      '#attributes' => ['class' => ['js-hide']],
    ];

    $form['content']['content_config'] = [
      '#type' => 'container',
    ];

    $content_plugin = $this->entityTabContentPluginManager->createInstance($entity_tab->get('content_plugin'), [
      'target_entity_type' => $targetEntityType,
    ]);
    $form['content']['content_config'] += $content_plugin->buildConfigurationForm([], $form_state);


    return $form;
  }

  /**
   * Handles switching the configuration type selector.
   */
  public function updateSelectedPluginType($form, FormStateInterface $form_state) {
    return $form['content'];
  }

  /**
   * Handles submit call when content plugin is selected.
   */
  public function submitSelectPlugin(array $form, FormStateInterface $form_state) {
    // Rebuild the entity using the form's new state.
    $this->entity = $this->buildEntity($form, $form_state);
    $form_state->setRebuild();
  }

  /**
   * Determines if the entity tab already exists.
   *
   * Callback for the machine_name form element.
   *
   * @param string|int $entity_id
   *   The entity ID.
   * @param array $element
   *   The form element.
   *
   * @return bool
   *   TRUE if the entity tab exists, FALSE otherwise.
   */
  public function exists($entity_id, array $element) {
    return (bool) $this->entityTypeManager->getStorage('entity_tab')
      ->getQuery()
      ->condition('id', $element['#field_prefix'] . $entity_id)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Sets the entity type ID into the config name for a new entity tab.
    if ($this->entity->isNew()) {
      $form_state->setValueForElement($form['id'], $this->entity->getTargetEntityTypeID() . '.' . $form_state->getValue('id'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_tab = $this->entity;
    $original_entity_tab = $this->entityTypeManager->getStorage('entity_tab')->load($entity_tab->id());

    $status = $entity_tab->save();

    if (empty($original_entity_tab)) {
      // On a new entity, rebuild the router and local tasks.
      $this->routerBuilder->setRebuildNeeded();
      $this->menuLocalTaskPluginManager->clearCachedDefinitions();
    }
    else {
      // On an existing entity, check whether values have changed.
      // A change in the path component requires a route rebuild.
      if ($original_entity_tab->getPathComponent() != $entity_tab->getPathComponent()) {
        $this->routerBuilder->setRebuildNeeded();
      }

      // A change in the tab title requires a local task rebuild.
      if ($original_entity_tab->getTabTitle() != $entity_tab->getTabTitle()) {
        $this->menuLocalTaskPluginManager->clearCachedDefinitions();
      }
    }

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Entity tab.', [
          '%label' => $entity_tab->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Entity tab.', [
          '%label' => $entity_tab->label(),
        ]));
    }

    // Redirect to the collection for the tab's target entity type.
    $target_entity_type_id = $entity_tab->getTargetEntityTypeID();
    $form_state->setRedirectUrl(Url::fromRoute("entity_ui.entity_tab.{$target_entity_type_id}.collection"));
  }

}
