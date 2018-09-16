<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.7.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Shell;

use Bit\Console\ConsoleOutput;
use Bit\Console\Shell;
use Bit\Core\Plugin;
use Bit\Utility\Inflector;
use SimpleXmlElement;

/**
 * Shows a list of commands available from the console.
 *
 */
class CommandListShell extends Shell
{

    /**
     * Contains tasks to load and instantiate
     *
     * @var array
     */
    public $tasks = ['Command'];

    /**
     * startup
     *
     * @return void
     */
    public function startup()
    {
        if (empty($this->params['xml'])) {
            parent::startup();
        }
    }

    /**
     * Main function Prints out the list of shells.
     *
     * @return void
     */
    public function main()
    {
        if (empty($this->params['xml'])) {
            $this->out("<info>Current Paths:</info>", 2);
            $this->out("* app:  " . APP_DIR);
            $this->out("* root: " . rtrim(ROOT, DIRECTORY_SEPARATOR));
            $this->out("* core: " . rtrim(CORE_PATH, DIRECTORY_SEPARATOR));
            $this->out("");

            $this->out("<info>Available Shells:</info>", 2);
        }

        $shellList = $this->Command->getShellList();
        if (empty($shellList)) {
            return;
        }

        if (empty($this->params['xml'])) {
            $this->_asText($shellList);
        } else {
            $this->_asXml($shellList);
        }
    }

    /**
     * Output text.
     *
     * @param array $shellList The shell list.
     * @return void
     */
    protected function _asText($shellList)
    {
        foreach ($shellList as $plugin => $commands) {
            sort($commands);
            $this->out(sprintf('[<info>%s</info>] %s', $plugin, implode(', ', $commands)));
            $this->out();
        }

        $this->out("To run an app or core command, type <info>`bit shell_name [args]`</info>");
        $this->out("To run a plugin command, type <info>`bit Plugin.shell_name [args]`</info>");
        $this->out("To get help on a specific command, type <info>`bit shell_name --help`</info>", 2);
    }

    /**
     * Output as XML
     *
     * @param array $shellList The shell list.
     * @return void
     */
    protected function _asXml($shellList)
    {
        $plugins = Plugin::loaded();
        $shells = new SimpleXmlElement('<shells></shells>');
        foreach ($shellList as $plugin => $commands) {
            foreach ($commands as $command) {
                $callable = $command;
                if (in_array($plugin, $plugins)) {
                    $callable = Inflector::camelize($plugin) . '.' . $command;
                }

                $shell = $shells->addChild('shell');
                $shell->addAttribute('name', $command);
                $shell->addAttribute('call_as', $callable);
                $shell->addAttribute('provider', $plugin);
                $shell->addAttribute('help', $callable . ' -h');
            }
        }
        $this->_io->outputAs(ConsoleOutput::RAW);
        $this->out($shells->saveXml());
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Bit\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->description(
            'Get the list of available shells for this BitPHP application.'
        )->addOption('xml', [
            'help' => 'Get the listing as XML.',
            'boolean' => true
        ]);

        return $parser;
    }
}
