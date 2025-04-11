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

/**
 * FilterInputFactoryInterface
 */
interface FilterInputFactoryInterface
{    
    /**
     * Returns the created filter input.
     *
     * @param array $config
     * @return FilterInputInterface
     */
    public function createFilterInput(array $config = []): FilterInputInterface;
}