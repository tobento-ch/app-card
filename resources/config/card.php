<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

use Psr\Container\ContainerInterface;
use Tobento\App\Card\Cards;
use Tobento\App\Card\CardsInterface;
use Tobento\App\Card\FilterInputFactory;
use Tobento\App\Card\FilterInputInterface;

return [
    
    /*
    |--------------------------------------------------------------------------
    | Interfaces
    |--------------------------------------------------------------------------
    |
    | Do not change the interface's names!
    |
    */
    
    'interfaces' => [
        FilterInputInterface::class => static function (FilterInputFactory $factory): FilterInputInterface {
            return $factory->createFilterInput([
                'storage' => 'cookie',
                // The duration in seconds until the cookie will expire.
                'lifetime' => null, // null|int
                'sameSite' => 'Strict',
                
                // You may use session storage instead:
                // 'storage' => 'session',
                // Make sure the \Tobento\App\Http\Boot\Session::class is booted on your app!
            ]);
        },
        
        CardsInterface::class => static function(ContainerInterface $container): CardsInterface {
            return new Cards(container: $container);
        }
    ],
];