<?php

namespace Drupal\entity_ui\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\entity_ui\Plugin\EntityTabContentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for editing and creating entity tab entities.
 */
class EntityTabForm extends EntityForm {

  protected $entityTabContentPluginManager;

  /**
   * Constructs a new EntityTabForm.
   *
   * @param \Drupal\entity_ui\Plugin\EntityTabContentManager
   *   The entity tab plugin manager.
   */
  public function __construct(EntityTabContentManager $entity_tab_content_manager) {
    $this->entityTabContentPluginManager = $entity_tab_content_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.entity_tab_content.processor')
    );
  }

  /**
   * The entity type for which the entity tab is being created.
   *
   * @var string
   */
   // ADD FORM ONLY TODO!
  protected $targetEntityTypeId;

  // entity_type.bundle.info

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
      $target_entity_type_id = $route_match->getParameter('entity_type_id');
      $values['targetEntityType'] = $target_entity_type_id;

      $entity = $this->entityTypeManager->getStorage($entity_type_id)->create($values);
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */ // change var name! TARGET
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL) {
    $form = parent::buildForm($form, $form_state);

    // EDIT FORM ONLY.
    if (empty($entity_type_id)) {
     // TODO! $entity_type_id = get it out of the entity!
    }
    else {
      $this->targetEntityTypeId = $entity_type_id;

      $target_entity_type = $this->entityManager->getDefinition($this->targetEntityTypeId);
      $form['#title'] = $this->t('Add new %label @entity-type', array(
        '%label' => $target_entity_type->getLabel(),
        '@entity-type' => $this->entityType->getLowercaseLabel(),
      ));
    }

    // ADD FORM ONLY TODO!

    // Change replace_pattern to avoid undesired dots.
    //$form['id']['#machine_name']['replace_pattern'] = '[^a-z0-9_]+';

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
      '#description' => $this->t("Label for the Entity tab."),
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

    $form['verb'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Permission verb'),
      '#maxlength' => 16,
      '#size' => 16,
      '#default_value' => $entity_tab->get('verb'),
      '#description' => $this->t("TODO."),
      '#required' => TRUE,
    ];

    $form['content'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Content options'),
      '#description' => $this->t('TODO  '),
      '#tree' => FALSE,
      '#prefix' => '<div id="content-settings-wrapper">',
      '#suffix' => '</div>',
    ];

    $options = [];
    foreach ($this->entityTabContentPluginManager->getDefinitions() as $plugin_id => $definition) {
      $options[$plugin_id] = $definition['label'];
    }
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
      '#weight' => 20,
      '#attributes' => ['class' => ['js-hide']],
    ];

    $form['content']['content_config'] = [
      '#type' => 'container',
      '#weight' => 21,
    ];

    $content_plugin = $this->entityTabContentPluginManager->createInstance($entity_tab->get('content_plugin'), []);
    dsm($content_plugin);
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
   * Handles submit call when sensor type is selected.
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
    return FALSE;
    // TODO
    return (bool) $this->queryFactory
      ->get($this->entity->getEntityTypeId())
      ->condition('id', $element['#field_prefix'] . $entity_id)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Sets the entity type ID into the config name.
    // TODO: this only needs to happen when adding a new entity.
    // ARGH where does this come from???????
    $form_state->setValueForElement($form['id'], $this->targetEntityTypeId . '.' . $form_state->getValue('id'));
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity_tab = $this->entity;
    $status = $entity_tab->save();

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
    $form_state->setRedirectUrl($entity_tab->urlInfo('collection'));
  }

}
