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

use JsonException;
use Stringable;
use Tobento\App\Card\CardInterface;
use Tobento\Service\Collection\Collection;
use Tobento\Service\Support\Htmlable;
use Tobento\Service\Support\Renderable;
use Tobento\Service\Support\Str;
use Tobento\Service\View\ViewInterface;

/**
 * KeyedList
 */
class KeyedList implements CardInterface
{
    /**
     * Create a new KeyedList instance.
     *
     * @param ViewInterface $view
     * @param array $items
     * @param string $group
     * @param int $priority
     */
    final public function __construct(
        protected ViewInterface $view,
        protected array $items,
        protected string $title = '',
        protected string $group = '',
        protected int $priority = 0,
    ) {}
    
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
     * Returns the content of the rendered card.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->view->render('card/keyedlist', ['card' => $this]);
    }
    
    /**
     * Returns the items.
     *
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }
    
    /**
     * Returns true if list is empty, otherwise false.
     *
     * @return bool
     */
    public function empty(): bool
    {
        return empty($this->items());
    }
    
    /**
     * Render a value.
     *
     * @param ViewInterface $view
     * @param mixed $value
     * @return string
     */
    public function renderValue(ViewInterface $view, mixed $value): string
    {
        if (is_array($value)) {
            try {
                $value = json_encode(
                    json_decode((new Collection($value))->toJson(), true, 512, JSON_THROW_ON_ERROR),
                    JSON_PRETTY_PRINT
                );
                return Str::esc($value);
            } catch (JsonException $e) {
                return '';
            }
        }

        if ($value instanceof CardInterface || $value instanceof Renderable) {
            return $value->render();
        }
        
        if ($value instanceof Htmlable) {
            return $value->toHtml();
        }
        
        if (is_string($value) || $value instanceof Stringable) {
            return Str::esc((string)$value);
        }
        
        if (is_numeric($value) || is_bool($value)) {
            return Str::esc((string)$value);
        }
        
        if (is_null($value)) {
            return 'null';
        }
        
        return '';
    }
}