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

namespace Tobento\App\Card;

use ArrayIterator;
use LogicException;
use Psr\Container\ContainerInterface;
use Tobento\Service\Autowire\Autowire;
use Traversable;

/**
 * Cards
 */
class Cards implements CardsInterface
{
    /**
     * @var Autowire
     */
    protected Autowire $autowire;
    
    /**
     * Create a new Cards instance.
     *
     * @param ContainerInterface $container
     * @param array $cards
     */
    public function __construct(
        ContainerInterface $container,
        protected array $cards = [],
    ) {
        $this->autowire = new Autowire($container);
    }
    
    /**
     * Add a card.
     *
     * @param string $name Must be lowercase and contain only [a-z-] characters.
     * @param string|CardInterface|CardFactoryInterface|callable $card
     * @return static $this
     */
    public function add(string $name, string|CardInterface|CardFactoryInterface|callable $card): static
    {
        $this->cards[$name] = $card;
        return $this;
    }
    
    /**
     * Add all cards from another CardsInterface.
     *
     * @param CardsInterface $cards
     * @return static $this
     */
    public function addFromCards(CardsInterface $cards): static
    {
        foreach($cards as $name => $card) {
            $this->add(name: $name, card: $card);
        }

        return $this;
    }
    
    /**
     * Returns true if card exists, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->cards);
    }
    
    /**
     * Returns a card by name.
     *
     * @param string $name
     * @return null|CardInterface
     */
    public function get(string $name): null|CardInterface
    {
        if (!isset($this->cards[$name])) {
            return null;
        }
        
        if ($this->cards[$name] instanceof CardInterface) {
            return $this->cards[$name];
        }
        
        // create card from callable:
        if (is_callable($this->cards[$name])) {
            return $this->cards[$name] = $this->autowire->call(
                $this->cards[$name],
                ['name' => $name]
            );
        }

        // create card from factory:
        if ($this->cards[$name] instanceof CardFactoryInterface) {
            return $this->cards[$name] = $this->cards[$name]->createCard(
                name: $name,
                container: $this->autowire->container(),
            );
        }
        
        if (is_string($this->cards[$name])) {
            return $this->cards[$name] = $this->autowire->resolve($this->cards[$name]);
        }
        
        return null;
    }
    
    /**
     * Returns a new instance with the filtered cards.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $new = clone $this;
        $new->cards = array_filter($this->all(), $callback);
        return $new;
    }
    
    /**
     * Returns a new instance with the specified group filtered.
     *
     * @param string $name
     * @return static
     */
    public function group(string $name): static
    {
        return $this->filter(fn(CardInterface $c): bool => $c->group() === $name);
    }
    
    /**
     * Returns a new instance with the specified card(s) only.
     *
     * @param string ...$name
     * @return static
     */
    public function only(string ...$name): static
    {
        $new = clone $this;
        
        $new->cards = array_filter(
            $this->cards,
            fn (string $key): bool => in_array($key, $name),
            ARRAY_FILTER_USE_KEY
        );
        
        return $new;
    }
    
    /**
     * Returns a new instance except the specified card(s).
     *
     * @param string ...$name
     * @return static
     */
    public function except(string ...$name): static
    {
        $new = clone $this;
        
        $new->cards = array_filter(
            $this->cards,
            fn (string $key): bool => !in_array($key, $name),
            ARRAY_FILTER_USE_KEY
        );
        
        return $new;
    }
    
    /**
     * Returns all card names.
     *
     * @return array<int, string>
     */
    public function names(): array
    {
        return array_keys($this->cards);
    }
    
    /**
     * Returns the number of cards.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->cards);
    }
    
    /**
     * Returns all cards.
     *
     * @return array<string, CardInterface>
     */
    public function all(): array
    {
        $cards = [];
        
        foreach($this->names() as $name) {
            if ($card = $this->get($name)) {
                $cards[$name] = $card;
            }
        }
        
        uasort($cards, fn (CardInterface $a, CardInterface $b): int => $b->priority() <=> $a->priority());
        
        return $cards;
    }
    
    /**
     * Get the iterator. 
     *
     * @return Traversable<string, CardInterface>
     */
    public function getIterator(): Traversable
    {    
        return new ArrayIterator($this->all());
    }
}