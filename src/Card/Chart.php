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

use Maantje\Charts\Chart as MaantjeChart;
use Stringable;
use Tobento\App\Card\CardInterface;
use Tobento\Service\View\ViewInterface;

class Chart implements CardInterface
{
    /**
     * Create a new Chart instance.
     *
     * @param ViewInterface $view
     * @param MaantjeChart $chart
     * @param string $title
     * @param string $group
     * @param int $priority
     */
    final public function __construct(
        protected ViewInterface $view,
        protected MaantjeChart $chart,
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
     * Returns the chart.
     *
     * @return MaantjeChart
     */
    public function chart(): MaantjeChart
    {
        return $this->chart;
    }
    
    /**
     * Returns the content of the rendered card.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->view->render('card/chart', ['card' => $this]);
    }
}