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
 
namespace Tobento\App\Card\Boot;

use Psr\Container\ContainerInterface;
use Tobento\App\Boot;
use Tobento\App\Boot\Config;
use Tobento\App\Migration\Boot\Migration;

/**
 * Card
 */
class Card extends Boot
{
    public const INFO = [
        'boot' => [
            'migrates card config, view and asset files',
            'implements cards interfaces based on the card config file',
        ],
    ];

    public const BOOT = [
        Config::class,
        Migration::class,
        \Tobento\App\Http\Boot\Routing::class,
        \Tobento\App\Http\Boot\RequesterResponser::class,
        \Tobento\App\Http\Boot\Cookies::class,
        \Tobento\App\View\Boot\View::class,
    ];

    /**
     * Boot application services.
     *
     * @param Config $config
     * @param Migration $migration
     * @return void
     */
    public function boot(Config $config, Migration $migration): void
    {
        // install migration:
        $migration->install(\Tobento\App\Card\Migration\Card::class);
        
        // load the card config:
        $config = $config->load('card.php');
        
        // setting interfaces:
        foreach($config['interfaces'] ?? [] as $interface => $implementation) {
            $this->app->set($interface, $implementation);
        }
    }
}