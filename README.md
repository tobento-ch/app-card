# App Card

The card app provides interfaces to create cards to be displayed on a dashboard page for instance. It comes with a default implementation and basic [cards](#cards).

## Table of Contents

- [Getting Started](#getting-started)
    - [Requirements](#requirements)
- [Documentation](#documentation)
    - [App](#app)
    - [Card Boot](#card-boot)
        - [Card Config](#card-config)
    - [Cards](#cards)
        - [Adding Cards](#adding-cards)
        - [Cards Methods](#cards-methods)
        - [Displaying Cards In Views](#displaying-cards-in-views)
        - [Creating Specific Cards](#creating-specific-cards)
    - [Available Cards](#available-cards)
        - [Chart Card](#chart-card)
        - [Group Card](#group-card)
        - [Html Card](#html-card)
        - [KeyedList Card](#keyedlist-card)
        - [Table Card](#table-card)
    - [Available Card Factories](#available-card-factories)
        - [Chart Card Factory](#chart-card-factory)
        - [Group Card Factory](#group-card-factory)
        - [Html Card Factory](#html-card-factory)
        - [KeyedList Card Factory](#keyedlist-card-factory)
        - [Table Card Factory](#table-card-factory)
    - [Filterable Cards](#filterable-cards)
- [Credits](#credits)
___

# Getting Started

Add the latest version of the app card project running this command.

```
composer require tobento/app-card
```

## Requirements

- PHP 8.4 or greater

# Documentation

## App

Check out the [**App Skeleton**](https://github.com/tobento-ch/app-skeleton) if you are using the skeleton.

You may also check out the [**App**](https://github.com/tobento-ch/app) to learn more about the app in general.

## Card Boot

The card boot does the following:

* migrates card config, view and asset files
* implements cards interfaces based on the card config file

```php
use Tobento\App\AppFactory;
use Tobento\App\Card\CardsInterface;
use Tobento\App\Card\FilterInputInterface;

// Create the app
$app = new AppFactory()->createApp();

// Add directories:
$app->dirs()
    ->dir(realpath(__DIR__.'/../'), 'root')
    ->dir(realpath(__DIR__.'/../app/'), 'app')
    ->dir($app->dir('app').'config', 'config', group: 'config')
    ->dir($app->dir('root').'public', 'public')
    ->dir($app->dir('root').'vendor', 'vendor');

// Adding boots:
$app->boot(\Tobento\App\Card\Boot\Card::class);
$app->booting();

// Implemented interfaces:
$cards = $app->get(CardsInterface::class);
$filterInput = $app->get(FilterInputInterface::class);

// Run the app
$app->run();
```

### Card Config

The configuration for the card is located in the ```app/config/card.php``` file at the default App Skeleton config location where you can configure the implemented card interfaces for your application.

## Cards

### Adding Cards

You may use the implemented ```CardsInterface::class``` adding cards to [display them later in your view files](#displaying-cards-in-views) or you may [create your own cards specific to a resource](#creating-specific-cards).

Furthermore, it is recommended to use the [App on](https://github.com/tobento-ch/app#on) method to add cards only if requested.

```php
use Tobento\App\Card\Card;
use Tobento\App\Card\CardInterface;
use Tobento\App\Card\CardsInterface;
use Tobento\App\Card\Factory;
use Tobento\Service\View\ViewInterface;

$app->on(
    CardsInterface::class,
    static function(CardsInterface $cards): void {
        // Using a card factory:
        $cards->add(name: 'foo', card: new Factory\Table(
            rows: [['foo']],
        ));
        
        // Using a callable being autowired:
        $cards->add(
            name: 'foo',
            card: static function (string $name, ViewInterface $view): CardInterface {
                return new Card\Table(
                    view: $view,
                    rows: [['foo']],
                );
            }
        );
        
        // Using a card instance:
        $cards->add(name: 'foo', card: new SomeCard());
        
        // Using a card class string being autowired:
        $cards->add(name: 'foo', card: SomeCard::class);
    }
);
```

Check out the [Available Cards](#available-cards) or [Available Card Factories](#available-card-factories).

### Cards Methods

**Filter Methods**

You may filter added cards using the following methods returning a new instance:

```php
use Tobento\App\Card\CardInterface;

// Returns a new instance with the filtered cards.
$cards = $cards->filter(fn(CardInterface $c): bool => $c->priority() > 1);

// Returns a new instance with the specified group filtered.
$cards = $cards->group(name: 'name');

// Returns a new instance with only the card(s) specified.
$cards = $cards->only('foo', 'bar');

// Returns a new instance except the specified card(s).
$cards = $cards->except('foo', 'bar');
```

**Retrieving Methods**

```php
// Returns true if card exists, otherwise false.
$cards->has(name: 'foo');

// Returns a card by name or null if not exists.
$card = $cards->get(name: 'foo');

// Returns all card names.
$cardNames = $cards->names();

// Returns the number of cards (int).
$numberOfCards = $cards->count();

// Returns all cards.
$cards = $cards->all(); // array<string, CardInterface>

// Iterating cards.
foreach($cards as $card) {}
```

### Displaying Cards In Views

In your view file, use the render method to display the cards:

```php
<!DOCTYPE html>
<html lang="<?= $view->esc($view->get('htmlLang', 'en')) ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Demo Cards</title>
        <?= $view->render('inc/head') ?>
        <?= $view->assets()->render() ?>
        <?php
        // add if you have or support filterable cards:
        $view->asset('assets/card/card.js')->attr('type', 'module');
        ?>
    </head>
    <body<?= $view->tagAttributes('body')->add('class', 'page')->render() ?>>
        <?= $view->render('inc/header') ?>
        <?= $view->render('inc/nav') ?>
        <main class="page-main">
            <?= $view->render('inc.breadcrumb') ?>
            <?= $view->render('inc.messages') ?>
            <h1 class="text-xl">Demo Cards</h1>
            
            <div class="cards">
                <?php
                foreach($cards as $card) {
                    echo $card->render();
                }
                ?>
            </div>
        </main>
        <?= $view->render('inc/footer') ?>
    </body>
</html>
```

Check out the [Cards - CSS basis](https://github.com/tobento-ch/css-basis#cards) to learn more about it.

Check out the [App View](https://github.com/tobento-ch/app-view) to learn more about it.

### Creating Specific Cards

Instead of using the implemented ```CardsInterface::class``` to [add cards](##adding-cards), you may create cards specific to a resource by simply extend the ```Cards:::class```:

```php
use Tobento\App\Card\Cards;

class DashboardCards extends Cards
{
    //
}
```

**Adding cards**

```php
use Tobento\App\Card\Factory;

$app->on(
    DashboardCards::class,
    static function(DashboardCards $cards): void {
        // Adding cards:
        $cards->add(name: 'foo', card: new Factory\Table(
            rows: ['foo', 'bar'],
        ));
    }
);
```

## Available Cards

### Chart Card

The ```Chart``` card may be used to display charts using the [Maantje Charts](https://github.com/maantje/charts) library:

```php
use Maantje\Charts\Chart;
use Maantje\Charts\Line\Line;
use Maantje\Charts\Line\Lines;
use Maantje\Charts\Line\Point;
use Tobento\App\Card\Card;
use Tobento\Service\View\ViewInterface;

$card = new Card\Chart(
    view: $view, // ViewInterface
    
    chart: new Chart(
        series: [
            new Lines(
                lines: [
                    new Line(
                        points: [
                            [0, 0],
                            [100, 4],
                            [200, 12],
                            [300, 8],
                        ]
                    ),
                    new Line(
                        points: [
                            new Point(x: 0, y: 4, color: 'red', size: 5),
                            new Point(x: 100, y: 12, color: 'red', size: 5),
                            new Point(x: 200, y: 24, color: 'red', size: 5),
                            new Point(x: 300, y: 7, color: 'red', size: 5),
                        ],
                        color: 'blue'
                    ),
                ]
            ),
        ],
    ),
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

### Group Card

The ```Group``` card may be used to group cards being displayed in smaller sizes:

```php
use Psr\Container\ContainerInterface;
use Tobento\App\Card\Card;
use Tobento\App\Card\CardFactoryInterface;
use Tobento\App\Card\CardInterface;
use Tobento\Service\View\ViewInterface;

$card = new Card\Group(
    container: $container, // ContainerInterface
    view: $view, // ViewInterface
    
    cards: [], // array<array-key, CardInterface|CardFactoryInterface>
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

You may use the [Group Card Factory](#group-card-factory) to [add the card](#adding-cards).

### Html Card

The ```Html``` card may be used to add HTML:

```php
use Tobento\App\Card\Card;
use Tobento\Service\View\ViewInterface;

$card = new Card\Html(
    view: $view, // ViewInterface
    
    // The html Must be escaped.
    html: 'html', // string|\Stringable
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

You may use the [Html Card Factory](#html-card-factory) to [add the card](#adding-cards).

### KeyedList Card

The ```KeyedList``` card may be used to add items being displayed as a keyed list:

```php
use Tobento\App\Card\Card;
use Tobento\Service\View\ViewInterface;

$card = new Card\KeyedList(
    view: $view, // ViewInterface
    
    items: [
        'key' => 'value',
    ],
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

You may use the [KeyedList Card Factory](#keyedlist-card-factory) to [add the card](#adding-cards).

### Table Card

The ```Table``` card may be used to add items being displayed in a table:

```php
use Tobento\App\Card\Card;
use Tobento\App\Card\Renderable\Link;
use Tobento\App\Card\Renderable\Links;
use Tobento\Service\View\ViewInterface;

$card = new Card\Table(
    view: $view, // ViewInterface
    
    // You may add table headers:
    headers: ['Description', 'Action'],
    
    // Add table rows:
    rows: [
        [
            'Desc',
            new Links(
                new Link(url: '/edit', label: 'Edit', attributes: ['class' => 'button']),
                new Link(url: '/view', label: 'View', attributes: ['class' => 'button']),
            ),
        ],
    ],
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

You may use the [Table Card Factory](#table-card-factory) to [add the card](#adding-cards).

## Available Card Factories

### Chart Card Factory

The ```Chart``` card factory creates a [Chart Card](#chart-card):

```php
use Maantje\Charts\Chart;
use Maantje\Charts\Line\Line;
use Maantje\Charts\Line\Lines;
use Maantje\Charts\Line\Point;
use Tobento\App\Card\Factory;

$factory = new Factory\Chart(
    chart: new Chart(
        series: [
            new Lines(
                lines: [
                    new Line(
                        points: [
                            [0, 0],
                            [100, 4],
                            [200, 12],
                            [300, 8],
                        ]
                    ),
                    new Line(
                        points: [
                            new Point(x: 0, y: 4, color: 'red', size: 5),
                            new Point(x: 100, y: 12, color: 'red', size: 5),
                            new Point(x: 200, y: 24, color: 'red', size: 5),
                            new Point(x: 300, y: 7, color: 'red', size: 5),
                        ],
                        color: 'blue'
                    ),
                ]
            ),
        ],
    ),
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

**CSS**

The following CSS is required to have responsive charts:

```css
.card-chart svg { 
  width: 100%;
  height: auto;
}
```

### Group Card Factory

The ```Group``` card factory creates a [Group Card](#group-card):

```php
use Tobento\App\Card\CardFactoryInterface;
use Tobento\App\Card\CardInterface;
use Tobento\App\Card\Factory;

$factory = new Factory\Group(
    cards: [], // array<array-key, CardInterface|CardFactoryInterface>
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

### Html Card Factory

The ```Html``` card factory creates a [Html Card](#html-card):

```php
use Tobento\App\Card\Factory;

$factory = new Factory\Html(
    // The html Must be escaped.
    html: 'html', // string|\Stringable
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

### KeyedList Card Factory

The ```KeyedList``` card factory creates a [KeyedList Card](#keyedlist-card):

```php
use Tobento\App\Card\Factory;

$factory = new Factory\KeyedList(
    items: [
        'key' => 'value',
    ],
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

### Table Card Factory

The ```Table``` card factory creates a [Table Card](#table-card):

```php
use Tobento\App\Card\Factory;
use Tobento\App\Card\Renderable\Link;

$factory = new Factory\Table(
    // You may add table headers:
    headers: ['Description', 'Action'],
    
    // Add table rows:
    rows: [
        ['Desc', new Link(url: 'Url', label: 'Label', attributes: ['class' => 'button'])],
    ],
    
    // You may set a title:
    title: 'A title',
    
    // You may set a group to be later filtered:
    group: 'main',
    
    // You may set a priority:
    priority: 100,
);
```

## Filterable Cards

You may create filterable cards by using the implemented ```FilterInputInterface``` storing filtered data in cookies (default) using the [App Http Cookies Boot](https://github.com/tobento-ch/app-http?#cookies-boot). You may consider [encrypting cookies](https://github.com/tobento-ch/app-http?#cookies-encryption) in addition.

```php
use Tobento\App\Card\CardInterface;
use Tobento\App\Card\FilterInputInterface;
use Tobento\Service\View\ViewInterface;

final class OrdersCard implements CardInterface
{
    public function __construct(
        private ViewInterface $view,
        private OrderRepository $orderRepository,
        private FilterInputInterface $input,
        private string $title = '',
        private string $group = '',
        private int $priority = 0,
    ) {}

    public function group(): string
    {
        return $this->group;
    }

    public function priority(): int
    {
        return $this->priority;
    }
    
    public function render(): string
    {
        return $this->view->render('card/orders', ['card' => $this]);
    }
    
    public function statuses(): array
    {
        return ['paid' => 'Paid', 'unpaid' => 'Unpaid'];
    }
    
    public function activeStatus(): string
    {
        $value = $this->input->get('order.status', '');
        
        // validate input as data may come from user input:
        if (in_array($value, array_keys($this->statuses()))) {
            return $value;
        }
        
        return 'paid';
    }
    
    public function getOrders(): array
    {
        return $this->orderRepository->findAll(
            where: [
                'status' => $this->activeStatus(),
            ],
            limit: 15,
        );
    }
    
    public function title(): string
    {
        return $this->title;
    }
}
```

**The ```card/orders``` view file:**

Make sure the names of the form elements are like ```card[name]```. In addition, make sure you have added the ```card.js``` asset in your main view which will update the cards content while filtering. See [Displaying Cards In Views](#displaying-cards-in-views) 

This example uses [Form Service](https://github.com/tobento-ch/service-form) which can be integrated by the [Form Boot](https://github.com/tobento-ch/app-view#form-boot).

```php
<div class="card">
    <?php if ($card->title()) { ?>
        <div class="card-head"><?= $view->esc($card->title()) ?></div>
    <?php } ?>
    <div class="card-body">
        <div class="card-filters">
            <?php
            $form = $view->form();
            echo $form->form(attributes: [
                'action' => '',
                'method' => 'GET',
                'data-card-filter' => 'orders',
            ]);
            echo $form->select(
                name: 'card.order.status',
                items: $card->statuses(),
                selected: $card->activeStatus(),
                selectAttributes: ['class' => 'small'],
            );
            echo $form->button(
                text: 'Filter',
                attributes: ['class' => 'display-none-if-js'],
                escText: true,
            );
            echo $form->close();
            ?>
        </div>
        <div data-card-content="orders">
            <?php
            foreach($card->getOrders() as $order) {
                // show order ...
            }
            ?>
        </div>
    </div>
</div>
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)
- [Maantje Charts](https://github.com/maantje/charts)