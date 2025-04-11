<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\App\Card\Test\Feature;

use Tobento\App\AppInterface;
use Tobento\App\Card\CardsInterface;
use Tobento\App\Card\Factory;
use Tobento\Service\Responser\ResponserInterface;
use Tobento\Service\Routing\RouterInterface;

class CardTest extends \Tobento\App\Testing\TestCase
{
    public function createApp(): AppInterface
    {
        $app = $this->createTmpApp(rootDir: __DIR__.'/../..');
        $app->boot(\Tobento\App\Card\Boot\Card::class);
        
        $app->on(
            CardsInterface::class,
            static function(CardsInterface $cards): void {
                $cards->add(name: 'foo', card: new Factory\Html(
                    html: '<p>Foo</p>',
                ));
                $cards->add(name: 'orders', card: App\OrdersCard::class);
            }
        );
        
        $app->on(RouterInterface::class, static function(RouterInterface $router): void {
            $router->get('cards', function (ResponserInterface $responser, CardsInterface $cards) {
                $html = '';
                foreach($cards as $name => $card) {
                    $html .= $card->render();
                }
                return $responser->html(
                    html: $html,
                    code: 200,
                );
            })->name('cards');
        });
        
        return $app;
    }

    public function testRenderCards()
    {
        $http = $this->fakeHttp();
        $http->request(method: 'GET', uri: 'cards');
        
        $http->response()
            ->assertStatus(200)
            ->assertBodyContains('<p>Foo</p>')
            ->assertBodyContains('paid:1,paid:2');
    }

    public function testCardIsFilteredUsingQueryParams()
    {
        $http = $this->fakeHttp();
        $http->request(
            method: 'GET',
            uri: 'cards',
            query: ['card' => ['order' => ['status' => 'unpaid']]],
        );
        
        $http->response()
            ->assertStatus(200)
            ->assertBodyContains('unpaid:1,unpaid:2');
    }
    
    public function testCardIsFilteredUsingCookieValue()
    {
        $http = $this->fakeHttp();
        $http->request(
            method: 'GET',
            uri: 'cards',
            cookies: [
                sprintf('card-%s', sha1('cards')) => json_encode(['order' => ['status' => 'unpaid']]),
            ],
        );
        
        $http->response()
            ->assertStatus(200)
            ->assertBodyContains('unpaid:1,unpaid:2');
    }
    
    public function testCardIsFilteredPrefersQueryParamsOverCookieValue()
    {
        $http = $this->fakeHttp();
        $http->request(
            method: 'GET',
            uri: 'cards',
            query: ['card' => ['order' => ['status' => 'unpaid']]],
            cookies: [
                sprintf('card-%s', sha1('cards')) => json_encode(['order' => ['status' => 'paid']]),
            ],
        );
        
        $http->response()
            ->assertStatus(200)
            ->assertBodyContains('unpaid:1,unpaid:2');
    }
}