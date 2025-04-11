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

namespace Tobento\App\Card\Card;

use Psr\Container\ContainerInterface;
use Tobento\App\Card\CardFactoryInterface;
use Tobento\App\Card\CardInterface;
use Tobento\Service\View\ViewInterface;

/**
 * Group
 */
class Group implements CardInterface
{
    /**
     * @var array<array-key, CardInterface>
     */
    protected array $cards = [];
    
    /**
     * Create a new Group instance.
     *
     * @param ContainerInterface $container
     * @param ViewInterface $view
     * @param array<array-key, CardInterface|CardFactoryInterface> $cards
     * @param string $title
     * @param string $group
     * @param int $priority
     */
    final public function __construct(
        ContainerInterface $container,
        protected ViewInterface $view,
        array $cards,
        protected string $title = '',
        protected string $group = '',
        protected int $priority = 0,
    ) {
        foreach($cards as $card) {
            if ($card instanceof CardFactoryInterface) {
                $card = $card->createCard(name: '', container: $container);
            }
            
            if ($card instanceof CardInterface) {
                $this->cards[] = $card;
            }
        }
    }
    
    /**
     * Returns the group of the card.
     *
     * @return string
     */
    public function group(): string
    {
        return $this->group;
    }
    
    /**
     * Returns the priority of the card.
     *
     * @return int
     */
    public function priority(): int
    {
        return $this->priority;
    }
    
    /**
     * Returns the title of the card.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
    
    /**
     * Returns the cards.
     *
     * @return array<array-key, CardInterface>
     */
    public function cards(): array
    {
        return $this->cards;
    }
    
    /**
     * Returns the content of the rendered card.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->view->render('card/group', ['card' => $this]);
    }
}