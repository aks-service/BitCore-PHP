<?php
namespace Bit\Shell\Task;

use Bit\Console\Shell;
use Bit\Filesystem\File;

/**
 * Task for unloading plugins.
 *
 */
class UnloadTask extends Shell
{

    /**
     * Path to the bootstrap file.
     *
     * @var string
     */
    public $bootstrap = null;

    /**
     * Execution method always used for tasks.
     *
     * @param string|null $plugin The plugin name.
     * @return bool if action passed.
     */
    public function main($plugin = null)
    {
        $this->bootstrap = ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php';

        if (empty($plugin)) {
            $this->err('<error>You must provide a plugin name in CamelCase format.</error>');
            $this->err('To unload an "Example" plugin, run <info>`bit plugin unload Example`</info>.');
            return false;
        }

        return (bool)$this->_modifyBootstrap($plugin);
    }

    /**
     * Update the applications bootstrap.php file.
     *
     * @param string $plugin Name of plugin.
     * @return bool If modify passed.
     */
    protected function _modifyBootstrap($plugin)
    {
        $finder = "/\nPlugin::load\((.|.\n|\n\s\s|\n\t|)+'$plugin'(.|.\n|)+\);\n/";

        $bootstrap = new File($this->bootstrap, false);
        $contents = $bootstrap->read();

        if (!preg_match("@\n\s*Plugin::loadAll@", $contents)) {
            $contents = preg_replace($finder, "", $contents);

            $bootstrap->write($contents);

            $this->out('');
            $this->out(sprintf('%s modified', $this->bootstrap));

            return true;
        }
        return false;
    }

    /**
     * GetOptionParser method.
     *
     * @return \Bit\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addArgument('plugin', [
            'help' => 'Name of the plugin to load.',
        ]);

        return $parser;
    }
}
