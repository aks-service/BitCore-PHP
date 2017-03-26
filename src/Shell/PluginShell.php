<?php
namespace Bit\Shell;

use Bit\Console\Shell;
use Bit\Core\Plugin;

/**
 * Shell for tasks related to plugins.
 *
 */
class PluginShell extends Shell
{

    /**
     * Tasks to load
     *
     * @var array
     */
    public $tasks = [
        'Assets',
        'Load',
        'Unload',
    ];

    /**
     * Displays all currently loaded plugins.
     *
     * @return void
     */
    public function loaded()
    {
        $loaded = Plugin::loaded();
        $this->out($loaded);
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Bit\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->description('Plugin Shell perform various tasks related to plugin.')
            ->addSubcommand('assets', [
                'help' => 'Symlink / copy plugin assets to app\'s webroot',
                'parser' => $this->Assets->getOptionParser()
            ])
            ->addSubcommand('loaded', [
                'help' => 'Lists all loaded plugins',
                'parser' => $parser,
            ])
            ->addSubcommand('load', [
                'help' => 'Loads a plugin',
                'parser' => $this->Load->getOptionParser(),
            ])
            ->addSubcommand('unload', [
                'help' => 'Unloads a plugin',
                'parser' => $this->Unload->getOptionParser(),
            ]);

        return $parser;
    }
}
