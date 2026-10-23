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
composer require mmitekk/staler-jsonld:^1.0
```

3. Включите модуль с помощью Drush:
```bash
drush en staler_jsonld -y
```

### Настройка и использование
1. Перейдите в административное меню: **Конфигурация** ➔ **Поиск и метатеги** ➔ **Staler JSON-LD** (`/admin/config/search/staler-jsonld`).
2. Заполните реквизиты организации и шаблон поиска — эти блоки работают сразу, без привязки к материалам.
3. Для товаров и статей у **каждого типа материала свой блок**: включите галочку у нужного типа и выберите из выпадающих списков его поля для зафиксированных слотов JSON-LD (описание, цена, артикул, изображение — для товаров; изображение, автор — для статей). Включить можно сколько угодно типов (лестницы, мешки, плёнки и т.д.) — все блоки отрисованы сразу, поля каждого блока соответствуют именно своему типу.
4. Хлебные крошки строятся автоматически по системной навигации Drupal: первый элемент — «Главная», дальше — элементы хлебной цепочки; пункты без ссылки (разделы-заглушки меню `<nolink>`) попадают в разметку с пустым значением `"item"`.
5. Сохраните настройки и очистите кэш: `drush cr`.

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
composer require mmitekk/staler-jsonld:^1.0
```

3. Enable the module via Drush:
```bash
drush en staler_jsonld -y
```

### Configuration and Usage
1. Go to administration menu: **Configuration** ➔ **Search and metadata** ➔ **Staler JSON-LD** (`/admin/config/search/staler-jsonld`).
2. Fill in the organization details and the search pattern — these blocks work out of the box, no content types involved.
3. For products and articles **every content type has its own block**: tick the checkbox next to the types that need markup and pick their fields from dropdowns for the fixed JSON-LD slots (description, price, SKU, image for products; image, author for articles). Any number of types can be enabled (ladders, bags, films, etc.) — all blocks are rendered at once, and each block lists the fields of its own type.
4. Breadcrumbs are built automatically from the Drupal breadcrumb trail: «Главная» (home) first, then each trail item; link-less items (menu section headers with `<nolink>`) are included with an empty `"item"` value.
5. Save configuration and clear cache: `drush cr`.

</details>
