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

use Stringable;
use Tobento\App\Card\CardInterface;
use Tobento\Service\View\ViewInterface;

/**
 * Html
 */
class Html implements CardInterface
{
    /**
     * Create a new Html instance.
     *
     * @param ViewInterface $view
     * @param string|Stringable $html Must be escaped.
     * @param string $title
     * @param string $group
     * @param int $priority
     */
    final public function __construct(
        protected ViewInterface $view,
        protected string|Stringable $html,
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
     * Returns the html.
     *
     * @return string
     */
    public function html(): string
    {
        return (string)$this->html;
    }
    
    /**
     * Returns the content of the rendered card.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->view->render('card/html', ['card' => $this]);
    }
}