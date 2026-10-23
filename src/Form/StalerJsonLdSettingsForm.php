<?php

namespace Drupal\staler_jsonld\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for Staler JSON-LD settings.
 */
class StalerJsonLdSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'staler_jsonld.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'staler_jsonld_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('staler_jsonld.settings');

    $form['organization'] = [
      '#type' => 'details',
      '#title' => $this->t('Organization & LocalBusiness Settings'),
      '#open' => TRUE,
    ];

    $form['organization']['org_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Organization Name'),
      '#default_value' => $config->get('org_name') ?? 'ООО Компания «Сталер»',
    ];

    $form['organization']['org_alt_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alternate Name / Brand'),
      '#default_value' => $config->get('org_alt_name') ?? 'Сталерпласт',
    ];

    $form['organization']['org_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Website Base URL'),
      '#default_value' => $config->get('org_url') ?? 'https://www.stalerplast.ru/',
    ];

    $form['organization']['org_logo'] = [
      '#type' => 'url',
      '#title' => $this->t('Logo URL'),
      '#default_value' => $config->get('org_logo') ?? 'https://www.stalerplast.ru/sites/default/files/staler144x144.png',
    ];

    $form['organization']['org_image'] = [
      '#type' => 'url',
      '#title' => $this->t('Organization Image URL'),
      '#default_value' => $config->get('org_image') ?? 'https://www.stalerplast.ru/sites/default/files/images/stalerplast.png',
    ];

    $form['organization']['org_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Telephone'),
      '#default_value' => $config->get('org_phone') ?? '+74952552261',
    ];

    $form['organization']['org_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#default_value' => $config->get('org_email') ?? 'info@stalerplast.ru',
    ];

    $form['organization']['address_street'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Street Address'),
      '#default_value' => $config->get('address_street') ?? 'Белокаменное шоссе, вл. 10/2',
    ];

    $form['organization']['address_locality'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City / Locality'),
      '#default_value' => $config->get('address_locality') ?? 'Видное',
    ];

    $form['organization']['address_region'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Region / Province'),
      '#default_value' => $config->get('address_region') ?? 'Московская область',
    ];

    $form['organization']['address_postal'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Postal Code'),
      '#default_value' => $config->get('address_postal') ?? '142703',
    ];

    $form['organization']['address_country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Country Code (ISO 2)'),
      '#default_value' => $config->get('address_country') ?? 'RU',
    ];

    $form['organization']['opening_hours'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening Hours (e.g. Mo-Fr 09:00-18:00)'),
      '#default_value' => $config->get('opening_hours') ?? 'Mo-Fr 09:00-18:00',
    ];

    $form['website'] = [
      '#type' => 'details',
      '#title' => $this->t('WebSite & SearchAction'),
      '#open' => TRUE,
    ];

    $form['website']['enable_website'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable WebSite and SearchAction JSON-LD on front page'),
      '#default_value' => $config->get('enable_website') ?? TRUE,
    ];

    $form['website']['search_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search URL Pattern'),
      '#description' => $this->t('Use {search_term_string} placeholder, e.g. https://www.stalerplast.ru/search?search_query={search_term_string}'),
      '#default_value' => $config->get('search_url') ?? 'https://www.stalerplast.ru/search?search_query={search_term_string}',
    ];

    $bundles = $this->nodeBundleOptions();
    $input = $form_state->getUserInput();

    // Selected bundle: AJAX user input first, then saved config, then fallback.
    $product_bundle = $input['product']['product_bundle'] ?? $config->get('product_bundle');
    if (!$product_bundle || !isset($bundles[$product_bundle])) {
      $product_bundle = isset($bundles['tovar']) ? 'tovar' : array_key_first($bundles);
    }
    $product_fields = $this->nodeFieldOptions($product_bundle);

    $form['product'] = [
      '#type' => 'details',
      '#title' => $this->t('Product & Offer Settings (Nodes)'),
      '#open' => TRUE,
    ];

    $form['product']['enable_product'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Product JSON-LD for product nodes'),
      '#default_value' => $config->get('enable_product') ?? TRUE,
    ];

    $form['product']['product_bundle'] = [
      '#type' => 'select',
      '#title' => $this->t('Product content type'),
      '#description' => $this->t('Field lists below are rebuilt for the selected content type.'),
      '#options' => $bundles,
      '#default_value' => $product_bundle,
      '#ajax' => [
        'callback' => '::updateProductFields',
        'wrapper' => 'staler-jsonld-product-fields',
        'event' => 'change',
      ],
    ];

    $form['product']['fields'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'staler-jsonld-product-fields'],
    ];

    $form['product']['fields']['product_field_description'] = [
      '#type' => 'select',
      '#title' => $this->t('Description field (JSON-LD "description")'),
      '#options' => $product_fields,
      '#default_value' => $this->storedOption($product_fields, $config->get('product_field_description'), 'body'),
    ];

    $form['product']['fields']['product_field_price'] = [
      '#type' => 'select',
      '#title' => $this->t('Price field (JSON-LD "price")'),
      '#options' => $product_fields,
      '#default_value' => $this->storedOption($product_fields, $config->get('product_field_price'), 'field_price'),
    ];

    $form['product']['fields']['product_field_sku'] = [
      '#type' => 'select',
      '#title' => $this->t('SKU field (JSON-LD "sku")'),
      '#options' => $product_fields,
      '#default_value' => $this->storedOption($product_fields, $config->get('product_field_sku'), 'field_sku'),
    ];

    $form['product']['fields']['product_field_image'] = [
      '#type' => 'select',
      '#title' => $this->t('Image field (JSON-LD "image")'),
      '#options' => $product_fields,
      '#default_value' => $this->storedOption($product_fields, $config->get('product_field_image'), 'field_image'),
    ];

    $article_bundle = $input['article']['article_bundle'] ?? $config->get('article_bundle');
    if (!$article_bundle || !isset($bundles[$article_bundle])) {
      $article_bundle = isset($bundles['article']) ? 'article' : array_key_first($bundles);
    }
    $article_fields = $this->nodeFieldOptions($article_bundle);

    $form['article'] = [
      '#type' => 'details',
      '#title' => $this->t('Article Settings'),
      '#open' => TRUE,
    ];

    $form['article']['enable_article'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Article JSON-LD for article nodes'),
      '#default_value' => $config->get('enable_article') ?? TRUE,
    ];

    $form['article']['article_bundle'] = [
      '#type' => 'select',
      '#title' => $this->t('Article content type'),
      '#options' => $bundles,
      '#default_value' => $article_bundle,
      '#ajax' => [
        'callback' => '::updateArticleFields',
        'wrapper' => 'staler-jsonld-article-fields',
        'event' => 'change',
      ],
    ];

    $form['article']['fields'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'staler-jsonld-article-fields'],
    ];

    $form['article']['fields']['article_field_image'] = [
      '#type' => 'select',
      '#title' => $this->t('Image field (JSON-LD "image")'),
      '#options' => $article_fields,
      '#default_value' => $this->storedOption($article_fields, $config->get('article_field_image'), ''),
    ];

    $form['article']['fields']['article_field_author'] = [
      '#type' => 'select',
      '#title' => $this->t('Author field (JSON-LD "author.name")'),
      '#description' => $this->t('Text field with the author name. If empty, the organization name is used.'),
      '#options' => $article_fields,
      '#default_value' => $this->storedOption($article_fields, $config->get('article_field_author'), ''),
    ];

    $form['breadcrumbs'] = [
      '#type' => 'details',
      '#title' => $this->t('BreadcrumbList Settings'),
      '#open' => TRUE,
    ];

    $form['breadcrumbs']['enable_breadcrumbs'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable BreadcrumbList JSON-LD automatically'),
      '#description' => $this->t('Built from the Drupal breadcrumb trail: «Главная» first, then each trail item. Items without a link (e.g. menu section headers) are included with an empty "item" value.'),
      '#default_value' => $config->get('enable_breadcrumbs') ?? TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * AJAX callback: rebuild product field selects for the chosen bundle.
   */
  public function updateProductFields(array &$form, FormStateInterface $form_state) {
    return $form['product']['fields'];
  }

  /**
   * AJAX callback: rebuild article field selects for the chosen bundle.
   */
  public function updateArticleFields(array &$form, FormStateInterface $form_state) {
    return $form['article']['fields'];
  }

  /**
   * Returns node type options keyed by machine name.
   */
  protected function nodeBundleOptions() {
    $options = [];
    foreach (\Drupal::service('entity_type.bundle.info')->getBundleInfo('node') as $machine => $info) {
      $options[$machine] = $info['label'] . ' (' . $machine . ')';
    }
    return $options;
  }

  /**
   * Returns field options (label + machine name) for a node bundle.
   */
  protected function nodeFieldOptions($bundle) {
    $options = ['' => '- ' . $this->t('None') . ' -'];
    if ($bundle) {
      foreach (\Drupal::service('entity_field.manager')->getFieldDefinitions('node', $bundle) as $name => $definition) {
        if ($definition->isInternal()) {
          continue;
        }
        $options[$name] = $definition->getLabel() . ' (' . $name . ')';
      }
    }
    return $options;
  }

  /**
   * Picks a saved option if it exists in the list, otherwise a fallback.
   */
  protected function storedOption(array $options, $stored, $fallback) {
    if ($stored && isset($options[$stored])) {
      return $stored;
    }
    return ($fallback !== '' && isset($options[$fallback])) ? $fallback : '';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('staler_jsonld.settings');
    $keys = [
      'org_name', 'org_alt_name', 'org_url', 'org_logo', 'org_image',
      'org_phone', 'org_email', 'address_street', 'address_locality',
      'address_region', 'address_postal', 'address_country', 'opening_hours',
      'enable_website', 'search_url',
      'enable_product', 'product_bundle', 'product_field_description',
      'product_field_price', 'product_field_sku', 'product_field_image',
      'enable_article', 'article_bundle', 'article_field_image',
      'article_field_author', 'enable_breadcrumbs',
    ];

    foreach ($keys as $key) {
      $config->set($key, $form_state->getValue($key));
    }

    $config->save();
    parent::submitForm($form, $form_state);
  }

}
