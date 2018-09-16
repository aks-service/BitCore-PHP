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

namespace Bit\Mailer;

use Bit\Core\Bit;
use Bit\Mailer\Exception\MissingMailerException;

/**
 * Provides functionality for loading mailer classes
 * onto properties of the host object.
 *
 * Example users of this trait are Bit\Controller\Controller and
 * Bit\Console\Shell.
 */
trait MailerAwareTrait
{

    /**
     * Returns a mailer instance.
     *
     * @param string $name Mailer's name.
     * @param \Bit\Mailer\Email|null $email Email instance.
     * @return \Bit\Mailer\Mailer
     * @throws \Bit\Mailer\Exception\MissingMailerException if undefined mailer class.
     */
    public function getMailer($name, Email $email = null)
    {
        if ($email === null) {
            $email = new Email();
        }

        $className = Bit::className($name, 'Mailer', 'Mailer');

        if (empty($className)) {
            throw new MissingMailerException(compact('name'));
        }

        return (new $className($email));
    }
}
