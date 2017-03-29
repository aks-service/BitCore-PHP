<?php
namespace Bit\I18n;

use Aura\Intl\Package;
use RuntimeException;

/**
 * Wraps multiple message loaders calling them one after another until
 * one of them returns a non-empty package.
 *
 */
class ChainMessagesLoader
{

    /**
     * The list of callables to execute one after another for loading messages
     *
     * @var array
     */
    protected $_loaders = [];

    /**
     * Receives a list of callable functions or objects that will be executed
     * one after another until one of them returns a non-empty translations package
     *
     * @param array $loaders List of callables to execute
     */
    public function __construct(array $loaders)
    {
        $this->_loaders = $loaders;
    }

    /**
     * Executes this object returning the translations package as configured in
     * the chain.
     *
     * @return \Aura\Intl\Package
     * @throws \RuntimeException if any of the loaders in the chain is not a valid callable
     */
    public function __invoke()
    {
        foreach ($this->_loaders as $k => $loader) {
            if (!is_callable($loader)) {
                throw new RuntimeException(sprintf(
                    'Loader "%s" in the chain is not a valid callable',
                    $k
                ));
            }

            $package = $loader();

            if (!$package) {
                continue;
            }

            if (!($package instanceof Package)) {
                throw new RuntimeException(sprintf(
                    'Loader "%s" in the chain did not return a valid Package object',
                    $k
                ));
            }

            if (count($package->getMessages())) {
                return $package;
            }
        }

        return new Package;
    }
}
