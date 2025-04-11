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

namespace Tobento\App\Card\Migration;

use Tobento\Service\Migration\Action\DirCopy;
use Tobento\Service\Migration\Action\DirDelete;
use Tobento\Service\Migration\Action\FilesCopy;
use Tobento\Service\Migration\Action\FilesDelete;
use Tobento\Service\Migration\Actions;
use Tobento\Service\Migration\ActionsInterface;
use Tobento\Service\Migration\MigrationInterface;
use Tobento\Service\Dir\DirsInterface;

/**
 * Card migration.
 */
class Card implements MigrationInterface
{
    /**
     * @var array The files.
     */
    protected array $files;
    
    /**
     * Create a new Card instance.
     *
     * @param DirsInterface $dirs
     */
    public function __construct(
        protected DirsInterface $dirs,
    ) {
        $resources = realpath(__DIR__.'/../../').'/resources/';
        
        $this->files = [
            $this->dirs->get('config') => [
                $resources.'/config/card.php',
            ],
        ];
    }
    
    /**
     * Return a description of the migration.
     *
     * @return string
     */
    public function description(): string
    {
        return 'Card view and asset files.';
    }
        
    /**
     * Return the actions to be processed on install.
     *
     * @return ActionsInterface
     */
    public function install(): ActionsInterface
    {
        $resources = realpath(__DIR__.'/../../').'/resources/';
        
        return new Actions(
            new FilesCopy(
                files: $this->files,
                type: 'config',
                description: 'Card config file.',
            ),
            new DirCopy(
                dir: $resources.'views/card/',
                destDir: $this->dirs->get('views').'card/',
                name: 'Card views',
                type: 'views',
                description: 'Card views.',
            ),
            new DirCopy(
                dir: $resources.'assets/card/',
                destDir: $this->dirs->get('public').'assets/card/',
                name: 'Card asset files',
                type: 'assets',
                description: 'Card asset files.',
            ),
        );
    }

    /**
     * Return the actions to be processed on uninstall.
     *
     * @return ActionsInterface
     */
    public function uninstall(): ActionsInterface
    {
        return new Actions(
            new FilesDelete(
                files: $this->files,
                type: 'config',
                description: 'Card config file.',
            ),
            new DirDelete(
                dir: $this->dirs->get('views').'card/',
                name: 'Card views',
                type: 'views',
                description: 'Card views.',
            ),
            new DirDelete(
                dir: $this->dirs->get('public').'assets/card/',
                name: 'Card asset files.',
                type: 'assets',
                description: 'Card asset files.',
            ),
        );
    }
}