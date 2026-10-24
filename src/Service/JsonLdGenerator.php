<?php

namespace Drupal\staler_jsonld\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service to generate Schema.org JSON-LD structured data.
 */
class JsonLdGenerator {

  protected $routeMatch;
  protected $entityTypeManager;
  protected $requestStack;
  protected $configFactory;

  /**
   * Constructs a JsonLdGenerator object.
   */
  public function __construct(RouteMatchInterface $route_match, EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack, ConfigFactoryInterface $config_factory) {
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entity_type_manager;
    $this->requestStack = $request_stack;
    $this->configFactory = $config_factory;
  }

  /**
   * Generates JSON-LD snippets for the current page.
   *
   * @return array
   *   Array of JSON-LD arrays/strings.
   */
  public function generate() {
    $config = $this->config('staler_jsonld.settings');
    $jsonld = [];
    $request = $this->requestStack->getCurrentRequest();
    $current_url = $request ? $request->getUri() : '';

    $org_id = ($config->get('org_url') ?: 'https://www.stalerplast.ru/') . '#organization';

    // 1. Organization & LocalBusiness (site-wide)
    $org_data = [
      '@context' => 'https://schema.org',
      '@type' => ['Organization', 'LocalBusiness'],
      '@id' => $org_id,
      'name' => $config->get('org_name') ?: 'ООО Компания «Сталер»',
      'alternateName' => $config->get('org_alt_name') ?: 'Сталерпласт',
      'url' => $config->get('org_url') ?: 'https://www.stalerplast.ru/',
      'logo' => $config->get('org_logo') ?: 'https://www.stalerplast.ru/sites/default/files/staler144x144.png',
      'image' => $config->get('org_image') ?: 'https://www.stalerplast.ru/sites/default/files/images/stalerplast.png',
      'telephone' => $config->get('org_phone') ?: '+74952552261',
      'email' => $config->get('org_email') ?: 'info@stalerplast.ru',
      'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $config->get('address_street') ?: 'Белокаменное шоссе, вл. 10/2',
        'addressLocality' => $config->get('address_locality') ?: 'Видное',
        'addressRegion' => $config->get('address_region') ?: 'Московская область',
        'postalCode' => $config->get('address_postal') ?: '142703',
        'addressCountry' => $config->get('address_country') ?: 'RU',
      ],
      'openingHoursSpecification' => [
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'opens' => '09:00',
        'closes' => '18:00',
      ],
    ];
    $jsonld[] = $org_data;

    // 2. WebSite & SearchAction (Front page)
    $is_front = \Drupal::service('path.matcher')->isFrontPage();
    if ($is_front && $config->get('enable_website')) {
      $website_data = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => ($config->get('org_url') ?: 'https://www.stalerplast.ru/') . '#website',
        'url' => $config->get('org_url') ?: 'https://www.stalerplast.ru/',
        'name' => $config->get('org_alt_name') ?: 'Сталерпласт',
        'publisher' => ['@id' => $org_id],
        'potentialAction' => [
          '@type' => 'SearchAction',
          'target' => $config->get('search_url') ?: 'https://www.stalerplast.ru/search?search_query={search_term_string}',
          'query-input' => 'required name=search_term_string',
        ],
      ];
      $jsonld[] = $website_data;
    }

    // 3. Entity specific JSON-LD (Node: Product or Article)
    $node = $this->routeMatch->getParameter('node');
    if ($node && is_object($node) && method_exists($node, 'getType')) {
      $type = $node->getType();

      // Product
      $product_mappings = $config->get('product_mappings') ?: [];
      $product_mapping = $product_mappings[$type] ?? NULL;
      if ($config->get('enable_product') && is_array($product_mapping) && !empty($product_mapping['enabled'])) {
        $description = $this->textFieldValue($node, $product_mapping['description'] ?? '');
        $price = $this->textFieldValue($node, $product_mapping['price'] ?? '');
        $sku = $this->textFieldValue($node, $product_mapping['sku'] ?? '');
        $img_url = $this->imageUrl($node, $product_mapping['image'] ?? '');

        $product_data = [
          '@context' => 'https://schema.org',
          '@type' => 'Product',
          'name' => $node->label(),
          'description' => $description ?: $node->label(),
          'sku' => $sku ?: (string) $node->id(),
          'brand' => [
            '@type' => 'Brand',
            'name' => $config->get('org_alt_name') ?: 'Сталерпласт',
          ],
          'offers' => [
            '@type' => 'Offer',
            'url' => $current_url,
            'priceCurrency' => 'RUB',
            'price' => $price ?: '0.00',
            'availability' => 'https://schema.org/InStock',
            'seller' => ['@id' => $org_id],
          ],
        ];
        if ($img_url) {
          $product_data['image'] = [$img_url];
        }
        $jsonld[] = $product_data;
      }

      // Article
      $article_mappings = $config->get('article_mappings') ?: [];
      $article_mapping = $article_mappings[$type] ?? NULL;
      if ($config->get('enable_article') && is_array($article_mapping) && !empty($article_mapping['enabled'])) {
        $article_data = [
          '@context' => 'https://schema.org',
          '@type' => 'Article',
          'headline' => $node->label(),
          'datePublished' => date('c', $node->getCreatedTime()),
          'dateModified' => date('c', $node->getChangedTime()),
          'author' => [
            '@type' => 'Organization',
            'name' => $config->get('org_name') ?: 'ООО Компания «Сталер»',
          ],
          'publisher' => ['@id' => $org_id],
          'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $current_url,
          ],
        ];

        $author_name = $this->textFieldValue($node, $article_mapping['author'] ?? '');
        if ($author_name !== '') {
          $article_data['author'] = [
            '@type' => 'Person',
            'name' => $author_name,
          ];
        }

        $img_url = $this->imageUrl($node, $article_mapping['image'] ?? '');
        if ($img_url) {
          $article_data['image'] = [$img_url];
        }

        $jsonld[] = $article_data;
      }
    }

    // 4. BreadcrumbList
    if ($config->get('enable_breadcrumbs') && !$is_front) {
      $breadcrumb_data = $this->generateBreadcrumbs();
      if ($breadcrumb_data) {
        $jsonld[] = $breadcrumb_data;
      }
    }

    return $jsonld;
  }

  /**
   * Builds BreadcrumbList JSON-LD from the Drupal breadcrumb trail.
   *
   * «Главная» is always the first item; trail items without a URL
   * (e.g. <nolink> menu section headers) keep an empty "item" value.
   */
  protected function generateBreadcrumbs() {
    $breadcrumb = \Drupal::service('breadcrumb')->build($this->routeMatch);
    $links = $breadcrumb->getLinks();

    $front_url = Url::fromRoute('<front>')->setAbsolute()->toString();
    $items = [
      [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => 'Главная',
        'item' => $front_url,
      ],
    ];

    $position = 2;
    foreach ($links as $link) {
      $url = $link->getUrl();
      if ($url->isRouted() && $url->getRouteName() === '<nolink>') {
        $href = '';
      }
      elseif ($url->isRouted()) {
        $href = $url->setAbsolute()->toString();
      }
      else {
        $href = $url->toString();
      }

      // The home link is already item 1 — skip its duplicate.
      if ($href !== '' && rtrim($href, '/') === rtrim($front_url, '/')) {
        continue;
      }

      $name = trim(strip_tags((string) $this->linkText($link)));
      if ($name === '') {
        continue;
      }

      // Elements without a URL (current page, <nolink> sections) omit the
      // "item" property entirely — an empty string breaks validators.
      $list_item = [
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => $name,
      ];
      if ($href !== '') {
        $list_item['item'] = $href;
      }
      $items[] = $list_item;
    }

    if (count($items) < 2) {
      return NULL;
    }

    return [
      '@context' => 'https://schema.org',
      '@type' => 'BreadcrumbList',
      'itemListElement' => $items,
    ];
  }

  /**
   * Returns a breadcrumb link text as a string (render arrays allowed).
   */
  protected function linkText($link) {
    $text = $link->getText();
    if (is_array($text)) {
      $text = (string) \Drupal::service('renderer')->render($text);
    }
    return $text;
  }

  /**
   * Returns the stripped text value of a node field (summary preferred).
   */
  protected function textFieldValue($node, $field_name) {
    if (!$field_name || !$node->hasField($field_name) || $node->get($field_name)->isEmpty()) {
      return '';
    }
    $item = $node->get($field_name)->first();
    if ($item === NULL) {
      return '';
    }
    $value = (string) ($item->value ?? '');
    $properties = $item->getFieldDefinition()->getFieldStorageDefinition()->getPropertyNames();
    if (in_array('summary', $properties, TRUE) && !empty($item->summary)) {
      $value = (string) $item->summary;
    }
    return trim(strip_tags($value));
  }

  /**
   * Returns the absolute URL of a node file/image field, or empty string.
   */
  protected function imageUrl($node, $field_name) {
    if (!$field_name || !$node->hasField($field_name) || $node->get($field_name)->isEmpty()) {
      return '';
    }
    $file = $node->get($field_name)->entity;
    if ($file && method_exists($file, 'getFileUri')) {
      return \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
    }
    return '';
  }

  /**
   * Helper to get config.
   */
  protected function config($name) {
    return $this->configFactory->get($name);
  }

}
