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
 * Table
 */
class Table implements CardInterface
{
    /**
     * Create a new Table.
     *
     * @param ViewInterface $view
     * @param array $headers
     * @param array $rows
     * @param string $title
     * @param string $group
     * @param int $priority
     */
    final public function __construct(
        protected ViewInterface $view,
        protected array $headers = [],
        protected array $rows = [],
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
        return $this->view->render('card/table', ['card' => $this]);
    }
    
    /**
     * Returns the headers.
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }
    
    /**
     * Returns the rows.
     *
     * @return array
     */
    public function rows(): array
    {
        return $this->rows;
    }
    
    /**
     * Returns true if table is empty, otherwise false.
     *
     * @return bool
     */
    public function empty(): bool
    {
        return empty($this->headers()) && empty($this->rows());
    }

    /**
     * Verify a row.
     *
     * @param mixed $row
     * @return array
     */
    public function verifyRow(mixed $row): array
    {
        if (is_array($row)) {
            return $row;
        }
        
        return (new Collection($row))->toArray();
    }
    
    /**
     * Render a value.
     *
     * @param ViewInterface $view
     * @param mixed $value
     * @param string|int $name
     * @return string
     */
    public function renderValue(ViewInterface $view, mixed $value, string|int $name): string
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