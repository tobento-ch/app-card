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

use Tobento\App\Card\CardInterface;

/**
 * NullCard
 */
class NullCard implements CardInterface
{
    /**
     * Returns the group of the card.
     *
     * @return string
     */
    public function group(): string
    {
        return '';
    }
    
    /**
     * Returns the priority of the card.
     *
     * @return int
     */
    public function priority(): int
    {
        return 0;
    }
    
    /**
     * Returns the content of the rendered card.
     *
     * @return string
     */
    public function render(): string
    {
        return '';
    }
}