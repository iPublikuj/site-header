<?php
/**
 * InvalidArgumentException.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:SiteHeader!
 * @subpackage     Exceptions
 * @since          1.0.0
 *
 * @date           28.01.17
 */

declare(strict_types = 1);

namespace IPub\SiteHeader\Exceptions;

class InvalidArgumentException extends \InvalidArgumentException implements IException
{
}
