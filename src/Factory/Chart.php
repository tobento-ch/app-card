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

namespace Tobento\App\Card\Factory;

use Maantje\Charts\Chart as MaantjeChart;
use Psr\Container\ContainerInterface;
use Stringable;
use Tobento\App\Card\Card;
use Tobento\App\Card\CardFactoryInterface;
use Tobento\App\Card\CardInterface;
use Tobento\Service\View\ViewInterface;

class Chart implements CardFactoryInterface
{
    /**
     * Create a new Chart instance.
     *
     * @param MaantjeChart $chart
     * @param string $title
     * @param string $group
     * @param int $priority
     */
    public function __construct(
        protected MaantjeChart $chart,
        protected string $title = '',
        protected string $group = '',
        protected int $priority = 0,
    ) {}
    
    /**
     * Returns the created card.
     *
     * @param string $name
     * @param ContainerInterface $container
     * @return CardInterface
     */
    public function createCard(string $name, ContainerInterface $container): CardInterface
    {
        return new Card\Chart(
            view: $container->get(ViewInterface::class),
            chart: $this->chart,
            title: $this->title,
            group: $this->group,
            priority: $this->priority,
        );
    }
}