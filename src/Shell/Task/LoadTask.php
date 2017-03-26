<?php
namespace Bit\Shell\Task;

use Bit\Console\Shell;
use Bit\Filesystem\File;

/**
 * Task for loading plugins.
 *
 */
class LoadTask extends Shell
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
     * @return bool
     */
    public function main($plugin = null)
    {
        $this->bootstrap = ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php';

        if (empty($plugin)) {
            $this->err('<error>You must provide a plugin name in CamelCase format.</error>');
            $this->err('To load an "Example" plugin, run <info>`bit plugin load Example`</info>.');
            return false;
        }

        return $this->_modifyBootstrap(
            $plugin,
            $this->params['bootstrap'],
            $this->params['routes'],
            $this->params['autoload']
        );
    }

    /**
     * Update the applications bootstrap.php file.
     *
     * @param string $plugin Name of plugin.
     * @param bool $hasBootstrap Whether or not bootstrap should be loaded.
     * @param bool $hasRoutes Whether or not routes should be loaded.
     * @param bool $hasAutoloader Whether or not there is an autoloader configured for
     * the plugin.
     * @return bool If modify passed.
     */
    protected function _modifyBootstrap($plugin, $hasBootstrap, $hasRoutes, $hasAutoloader)
    {
        $bootstrap = new File($this->bootstrap, false);
        $contents = $bootstrap->read();
        if (!preg_match("@\n\s*Plugin::loadAll@", $contents)) {
            $autoloadString = $hasAutoloader ? "'autoload' => true" : '';
            $bootstrapString = $hasBootstrap ? "'bootstrap' => true" : '';
            $routesString = $hasRoutes ? "'routes' => true" : '';

            $append = "\nPlugin::load('%s', [%s]);\n";
            $options = implode(', ', array_filter([$autoloadString, $bootstrapString, $routesString]));

            $bootstrap->append(str_replace(', []', '', sprintf($append, $plugin, $options)));
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

        $parser->addOption('bootstrap', [
                    'short' => 'b',
                    'help' => 'Will load bootstrap.php from plugin.',
                    'boolean' => true,
                    'default' => false,
                ])
                ->addOption('routes', [
                    'short' => 'r',
                    'help' => 'Will load routes.php from plugin.',
                    'boolean' => true,
                    'default' => false,
                ])
                ->addOption('autoload', [
                    'help' => 'Will autoload the plugin using BitPHP. ' .
                        'Set to true if you are not using composer to autoload your plugin.',
                    'boolean' => true,
                    'default' => false,
                ])
                ->addArgument('plugin', [
                    'help' => 'Name of the plugin to load.',
                ]);

        return $parser;
    }
}
