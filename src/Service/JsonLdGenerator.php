<?php

namespace Drupal\staler_jsonld\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
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

    // 1. Organization & LocalBusiness (site-wide or front page)
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
      $product_bundles = array_map('trim', explode(',', $config->get('product_bundles') ?: 'tovar'));
      if ($config->get('enable_product') && in_array($type, $product_bundles)) {
        $price_field = $config->get('field_price') ?: 'field_price';
        $sku_field = $config->get('field_sku') ?: 'field_sku';
        $image_field = $config->get('field_image') ?: 'field_image';

        $price = '';
        if ($node->hasField($price_field) && !$node->get($price_field)->isEmpty()) {
          $price = $node->get($price_field)->value;
        }

        $sku = '';
        if ($node->hasField($sku_field) && !$node->get($sku_field)->isEmpty()) {
          $sku = $node->get($sku_field)->value;
        }

        $img_url = '';
        if ($node->hasField($image_field) && !$node->get($image_field)->isEmpty()) {
          $file = $node->get($image_field)->entity;
          if ($file && method_exists($file, 'getFileUri')) {
            $img_url = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
          }
        }

        $product_data = [
          '@context' => 'https://schema.org',
          '@type' => 'Product',
          'name' => $node->label(),
          'description' => !$node->get('body')->isEmpty() ? strip_tags($node->get('body')->summary ?: $node->get('body')->value) : $node->label(),
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
      $article_bundles = array_map('trim', explode(',', $config->get('article_bundles') ?: 'article'));
      if ($config->get('enable_article') && in_array($type, $article_bundles)) {
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
        $jsonld[] = $article_data;
      }
    }

    // 4. BreadcrumbList
    if ($config->get('enable_breadcrumbs') && !$is_front) {
      $breadcrumb_builder = \Drupal::service('breadcrumb');
      $breadcrumb = $breadcrumb_builder->build($this->routeMatch);
      $links = $breadcrumb->getLinks();

      if (!empty($links)) {
        $list_element = [];
        $position = 1;
        // Home link
        $list_element[] = [
          '@type' => 'ListItem',
          'position' => $position++,
          'name' => 'Главная',
          'item' => $config->get('org_url') ?: 'https://www.stalerplast.ru/',
        ];

        foreach ($links as $link) {
          $url = $link->getUrl();
          if ($url->isRouted()) {
            $url = $url->setAbsolute()->toString();
          } else {
            $url = $url->toString();
          }
          $list_element[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $link->getText(),
            'item' => $url,
          ];
        }

        $breadcrumb_data = [
          '@context' => 'https://schema.org',
          '@type' => 'BreadcrumbList',
          'itemListElement' => $list_element,
        ];
        $jsonld[] = $breadcrumb_data;
      }
    }

    return $jsonld;
  }

  /**
   * Helper to get config.
   */
  protected function config($name) {
    return $this->configFactory->get($name);
  }

}
