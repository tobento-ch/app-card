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
use Tobento\App\Card\FilterInputFactory;
use Tobento\App\Card\FilterInputInterface;
use Tobento\Service\Responser\ResponserInterface;
use Tobento\Service\Routing\RouterInterface;

class CardSessionTest extends \Tobento\App\Testing\TestCase
{
    use \Tobento\App\Testing\Http\RefreshSession;
    
    public function createApp(): AppInterface
    {
        $app = $this->createTmpApp(rootDir: __DIR__.'/../..');
        $app->boot(\Tobento\App\Http\Boot\Session::class);
        $app->boot(\Tobento\App\Card\Boot\Card::class);
        
        $app->on(
            FilterInputInterface::class,
            static function (FilterInputInterface $input, FilterInputFactory $factory): FilterInputInterface {
                return $factory->createFilterInput([
                    'storage' => 'session',
                ]);
            }
        );
            
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
    
    public function testCardCanBeFiltered()
    {
        $http = $this->fakeHttp();
        $http->request(
            method: 'GET',
            uri: 'cards',
            query: ['card' => ['order' => ['status' => 'unpaid']]],
        );
        $http->response()->assertStatus(200)->assertBodyContains('unpaid:1,unpaid:2');
        
        // using session:
        $http->request(method: 'GET', uri: 'cards');
        $http->response()->assertBodyContains('unpaid:1,unpaid:2');
        
        $http->request(method: 'GET', uri: 'cards');
        $http->response()->assertBodyContains('unpaid:1,unpaid:2');
        
        // prefers query params over session:
        $http->request(
            method: 'GET',
            uri: 'cards',
            query: ['card' => ['order' => ['status' => 'paid']]],
        );
        $http->response()->assertStatus(200)->assertBodyContains('paid:1,paid:2');
    }
}