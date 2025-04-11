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

use Psr\Container\ContainerInterface;

/**
 * CardFactoryInterface
 */
interface CardFactoryInterface
{
    /**
     * Returns the created card.
     *
     * @param string $name
     * @param ContainerInterface $container
     * @return CardInterface
     */
    public function createCard(string $name, ContainerInterface $container): CardInterface;
}