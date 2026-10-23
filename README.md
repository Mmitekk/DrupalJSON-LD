# Staler JSON-LD for Drupal 10 / 11

<details open>
<summary><b>🇷🇺 Русский</b></summary>

## О модуле
**Staler JSON-LD** — профессиональный модуль микроразметки Schema.org для Drupal 10 и 11. Автоматически внедряет структурированные данные JSON-LD в тег `<head>` страниц сайта в зависимости от контекста (главная, карточки товаров, статьи, хлебные крошки, организация).

### Возможности
- 🏢 **Organization & LocalBusiness**: Полная разметка компании с адресом, телефонами, логотипом и часами работы.
- 🔍 **WebSite & SearchAction**: Интеграция поисковой строки сайта в поисковую выдачу Google/Yandex.
- 📦 **Product & Offer**: Автоматическое чтение цены, SKU и изображений из полей ноды товара (`tovar`, `product`).
- 📰 **Article**: Структурированные данные для статей и новостей (`article`).
- 🧭 **BreadcrumbList**: Автоматическая генерация хлебных крошек на основе системы путей Drupal.
- 🎛 **Административная панель**: Удобная настройка всех параметров прямо из админки Drupal (`/admin/config/search/staler-jsonld`).

### Установка через Composer
Выполните следующие команды в корне вашего проекта Drupal:

1. Добавьте репозиторий через Composer CLI:
```bash
composer config repositories.drupal_json_ld vcs https://github.com/Mmitekk/DrupalJSON-LD
```

2. Установите релизную версию модуля:
```bash
composer require mitekk/staler-jsonld:^1.0
```

3. Включите модуль с помощью Drush:
```bash
drush en staler_jsonld -y
```

### Настройка и использование
1. Перейдите в административное меню: **Конфигурация** ➔ **Поиск и метатеги** ➔ **Staler JSON-LD** (`/admin/config/search/staler-jsonld`).
2. Заполните реквизиты компании, укажите машинные имена нод товаров и статей, а также настройте маппинг полей (`field_price`, `field_sku`, `field_image`).
3. Сохраните настройки и очистите кэш: `drush cr`.

</details>

<details>
<summary><b>🇬🇧 English</b></summary>

## About the Module
**Staler JSON-LD** is a comprehensive Schema.org structured data module for Drupal 10 and 11. It automatically injects JSON-LD structured data into the page `<head>` based on the current context (frontpage, product nodes, articles, breadcrumbs, organization).

### Features
- 🏢 **Organization & LocalBusiness**: Complete company markup with address, phone numbers, logo, and operating hours.
- 🔍 **WebSite & SearchAction**: Integration of site search box in Google and Yandex search results.
- 📦 **Product & Offer**: Automatic extraction of price, SKU, and images from product content types (`tovar`, `product`).
- 📰 **Article**: Structured data for articles and news posts (`article`).
- 🧭 **BreadcrumbList**: Automatic breadcrumb generation based on Drupal route matching.
- 🎛 **Admin Panel**: Easy configuration of all parameters directly via Drupal admin (`/admin/config/search/staler-jsonld`).

### Installation via Composer
Run the following commands in your Drupal project root:

1. Add the repository via Composer CLI:
```bash
composer config repositories.drupal_json_ld vcs https://github.com/Mmitekk/DrupalJSON-LD
```

2. Require the release version via Composer:
```bash
composer require mitekk/staler-jsonld:^1.0
```

3. Enable the module via Drush:
```bash
drush en staler_jsonld -y
```

### Configuration and Usage
1. Go to administration menu: **Configuration** ➔ **Search and metadata** ➔ **Staler JSON-LD** (`/admin/config/search/staler-jsonld`).
2. Fill in company details, specify product and article content type machine names, and field mappings (`field_price`, `field_sku`, `field_image`).
3. Save configuration and clear cache: `drush cr`.

</details>
