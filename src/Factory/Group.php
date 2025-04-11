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

use Psr\Container\ContainerInterface;
use Tobento\App\Card\Card;
use Tobento\App\Card\CardFactoryInterface;
use Tobento\App\Card\CardInterface;
use Tobento\Service\View\ViewInterface;

/**
 * Group
 */
class Group implements CardFactoryInterface
{
    /**
     * Create a new Group instance.
     *
     * @param array<array-key, CardInterface|CardFactoryInterface> $cards
     * @param string $title
     * @param string $group
     * @param int $priority
     */
    public function __construct(
        protected array $cards,
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
        return new Card\Group(
            container: $container,
            view: $container->get(ViewInterface::class),
            cards: $this->cards,
            title: $this->title,
            group: $this->group,
            priority: $this->priority,
        );
    }
}