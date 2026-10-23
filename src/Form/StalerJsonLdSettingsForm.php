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

    $form['product']['product_bundles'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Product Content Type Machine Names (comma separated)'),
      '#description' => $this->t('e.g. tovar, product'),
      '#default_value' => $config->get('product_bundles') ?? 'tovar',
    ];

    $form['product']['field_price'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Price Field Machine Name'),
      '#default_value' => $config->get('field_price') ?? 'field_price',
    ];

    $form['product']['field_sku'] = [
      '#type' => 'textfield',
      '#title' => $this->t('SKU Field Machine Name (optional)'),
      '#default_value' => $config->get('field_sku') ?? 'field_sku',
    ];

    $form['product']['field_image'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Image Field Machine Name'),
      '#default_value' => $config->get('field_image') ?? 'field_image',
    ];

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

    $form['article']['article_bundles'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Article Content Type Machine Names (comma separated)'),
      '#default_value' => $config->get('article_bundles') ?? 'article',
    ];

    $form['breadcrumbs'] = [
      '#type' => 'details',
      '#title' => $this->t('BreadcrumbList Settings'),
      '#open' => TRUE,
    ];

    $form['breadcrumbs']['enable_breadcrumbs'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable BreadcrumbList JSON-LD automatically'),
      '#default_value' => $config->get('enable_breadcrumbs') ?? TRUE,
    ];

    return parent::buildForm($form, $form_state);
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
      'enable_website', 'search_url', 'enable_product', 'product_bundles',
      'field_price', 'field_sku', 'field_image', 'enable_article',
      'article_bundles', 'enable_breadcrumbs'
    ];

    foreach ($keys as $key) {
      $config->set($key, $form_state->getValue($key));
    }

    $config->save();
    parent::submitForm($form, $form_state);
  }

}
