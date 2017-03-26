<?php
namespace Bit\Console;

use SimpleXmlElement;

/**
 * An object to represent a single subcommand used in the command line.
 * Created when you call ConsoleOptionParser::addSubcommand()
 *
 * @see \Bit\Console\ConsoleOptionParser::addSubcommand()
 */
class ConsoleInputSubcommand
{

    /**
     * Name of the subcommand
     *
     * @var string
     */
    protected $_name;

    /**
     * Help string for the subcommand
     *
     * @var string
     */
    protected $_help;

    /**
     * The ConsoleOptionParser for this subcommand.
     *
     * @var \Bit\Console\ConsoleOptionParser
     */
    protected $_parser;

    /**
     * Make a new Subcommand
     *
     * @param string|array $name The long name of the subcommand, or an array with all the properties.
     * @param string $help The help text for this option.
     * @param \Bit\Console\ConsoleOptionParser|array|null $parser A parser for this subcommand. Either a ConsoleOptionParser, or an
     *   array that can be used with ConsoleOptionParser::buildFromArray().
     */
    public function __construct($name, $help = '', $parser = null)
    {
        if (is_array($name) && isset($name['name'])) {
            foreach ($name as $key => $value) {
                $this->{'_' . $key} = $value;
            }
        } else {
            $this->_name = $name;
            $this->_help = $help;
            $this->_parser = $parser;
        }
        if (is_array($this->_parser)) {
            $this->_parser['command'] = $this->_name;
            $this->_parser = ConsoleOptionParser::buildFromArray($this->_parser);
        }
    }

    /**
     * Get the value of the name attribute.
     *
     * @return string Value of this->_name.
     */
    public function name()
    {
        return $this->_name;
    }

    /**
     * Generate the help for this this subcommand.
     *
     * @param int $width The width to make the name of the subcommand.
     * @return string
     */
    public function help($width = 0)
    {
        $name = $this->_name;
        if (strlen($name) < $width) {
            $name = str_pad($name, $width, ' ');
        }
        return $name . $this->_help;
    }

    /**
     * Get the usage value for this option
     *
     * @return mixed Either false or a ConsoleOptionParser
     */
    public function parser()
    {
        if ($this->_parser instanceof ConsoleOptionParser) {
            return $this->_parser;
        }
        return false;
    }

    /**
     * Append this subcommand to the Parent element
     *
     * @param \SimpleXmlElement $parent The parent element.
     * @return \SimpleXmlElement The parent with this subcommand appended.
     */
    public function xml(SimpleXmlElement $parent)
    {
        $command = $parent->addChild('command');
        $command->addAttribute('name', $this->_name);
        $command->addAttribute('help', $this->_help);
        return $parent;
    }
}
