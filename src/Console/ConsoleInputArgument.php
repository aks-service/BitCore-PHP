<?php
namespace Bit\Console;

use Bit\Console\Exception\ConsoleException;
use SimpleXmlElement;

/**
 * An object to represent a single argument used in the command line.
 * ConsoleOptionParser creates these when you use addArgument()
 *
 * @see \Bit\Console\ConsoleOptionParser::addArgument()
 */
class ConsoleInputArgument
{

    /**
     * Name of the argument.
     *
     * @var string
     */
    protected $_name;

    /**
     * Help string
     *
     * @var string
     */
    protected $_help;

    /**
     * Is this option required?
     *
     * @var bool
     */
    protected $_required;

    /**
     * An array of valid choices for this argument.
     *
     * @var array
     */
    protected $_choices;

    /**
     * Make a new Input Argument
     *
     * @param string|array $name The long name of the option, or an array with all the properties.
     * @param string $help The help text for this option
     * @param bool $required Whether this argument is required. Missing required args will trigger exceptions
     * @param array $choices Valid choices for this option.
     */
    public function __construct($name, $help = '', $required = false, $choices = [])
    {
        if (is_array($name) && isset($name['name'])) {
            foreach ($name as $key => $value) {
                $this->{'_' . $key} = $value;
            }
        } else {
            $this->_name = $name;
            $this->_help = $help;
            $this->_required = $required;
            $this->_choices = $choices;
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
     * Checks if this argument is equal to another argument.
     *
     * @param \Bit\Console\ConsoleInputArgument $argument ConsoleInputArgument to compare to.
     * @return bool
     */
    public function isEqualTo(ConsoleInputArgument $argument)
    {
        return $this->usage() === $argument->usage();
    }

    /**
     * Generate the help for this argument.
     *
     * @param int $width The width to make the name of the option.
     * @return string
     */
    public function help($width = 0)
    {
        $name = $this->_name;
        if (strlen($name) < $width) {
            $name = str_pad($name, $width, ' ');
        }
        $optional = '';
        if (!$this->isRequired()) {
            $optional = ' <comment>(optional)</comment>';
        }
        if (!empty($this->_choices)) {
            $optional .= sprintf(' <comment>(choices: %s)</comment>', implode('|', $this->_choices));
        }
        return sprintf('%s%s%s', $name, $this->_help, $optional);
    }

    /**
     * Get the usage value for this argument
     *
     * @return string
     */
    public function usage()
    {
        $name = $this->_name;
        if (!empty($this->_choices)) {
            $name = implode('|', $this->_choices);
        }
        $name = '<' . $name . '>';
        if (!$this->isRequired()) {
            $name = '[' . $name . ']';
        }
        return $name;
    }

    /**
     * Check if this argument is a required argument
     *
     * @return bool
     */
    public function isRequired()
    {
        return (bool)$this->_required;
    }

    /**
     * Check that $value is a valid choice for this argument.
     *
     * @param string $value The choice to validate.
     * @return bool
     * @throws \Bit\Console\Exception\ConsoleException
     */
    public function validChoice($value)
    {
        if (empty($this->_choices)) {
            return true;
        }
        if (!in_array($value, $this->_choices)) {
            throw new ConsoleException(
                sprintf(
                    '"%s" is not a valid value for %s. Please use one of "%s"',
                    $value,
                    $this->_name,
                    implode(', ', $this->_choices)
                )
            );
        }
        return true;
    }

    /**
     * Append this arguments XML representation to the passed in SimpleXml object.
     *
     * @param \SimpleXmlElement $parent The parent element.
     * @return \SimpleXmlElement The parent with this argument appended.
     */
    public function xml(SimpleXmlElement $parent)
    {
        $option = $parent->addChild('argument');
        $option->addAttribute('name', $this->_name);
        $option->addAttribute('help', $this->_help);
        $option->addAttribute('required', $this->isRequired());
        $choices = $option->addChild('choices');
        foreach ($this->_choices as $valid) {
            $choices->addChild('choice', $valid);
        }
        return $parent;
    }
}
