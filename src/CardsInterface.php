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

use IteratorAggregate;
use Countable;

/**
 * @extends IteratorAggregate<string, CardInterface>
 */
interface CardsInterface extends IteratorAggregate, Countable
{
    /**
     * Add a card.
     *
     * @param string $name Must be lowercase and contain only [a-z-] characters.
     * @param string|CardInterface|CardFactoryInterface|callable $card
     * @return static $this
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function add(string $name, string|CardInterface|CardFactoryInterface|callable $card): static;
    
    /**
     * Add all cards from another CardsInterface.
     *
     * @param CardsInterface $cards
     * @return static $this
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function addFromCards(CardsInterface $cards): static;
    
    /**
     * Returns true if card exists, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
    
    /**
     * Returns a card by name.
     *
     * @param string $name
     * @return null|CardInterface
     */
    public function get(string $name): null|CardInterface;
    
    /**
     * Returns a new instance with the filtered cards.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static;
    
    /**
     * Returns a new instance with the specified group filtered.
     *
     * @param string $name
     * @return static
     */
    public function group(string $name): static;
    
    /**
     * Returns a new instance with the specified card(s) only.
     *
     * @param string ...$name
     * @return static
     */
    public function only(string ...$name): static;
    
    /**
     * Returns a new instance except the specified card(s).
     *
     * @param string ...$name
     * @return static
     */
    public function except(string ...$name): static;
    
    /**
     * Returns all card names.
     *
     * @return array<int, string>
     */
    public function names(): array;
    
    /**
     * Returns the number of cards.
     *
     * @return int
     */
    public function count(): int;
    
    /**
     * Returns all cards.
     *
     * @return array<string, CardInterface>
     */
    public function all(): array;
}