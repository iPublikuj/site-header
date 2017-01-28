<?php
/**
 * InvalidArgumentException.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:SiteHeader!
 * @subpackage     Exceptions
 * @since          1.0.0
 *
 * @date           28.01.17
 */

declare(strict_types = 1);

namespace IPub\SiteHeader\Exceptions;

use Nette;

class InvalidArgumentException extends Nette\InvalidArgumentException implements IException
{
}
