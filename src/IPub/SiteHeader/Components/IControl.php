<?php
/**
 * IControl.php
 *
 * @copyright      More in license.md
 * @license        http://www.fastybird.com
 * @author         Adam Kadlec http://www.fastybird.com
 * @package        iPublikuj:SiteHeader!
 * @subpackage     Components
 * @since          1.0.0
 *
 * @date           12.03.14
 */

declare(strict_types = 1);

namespace IPub\SiteHeader\Components;

/**
 * Control factory
 *
 * @package        iPublikuj:SiteHeader!
 * @subpackage     Components
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IControl
{
	/**
	 * @return Control
	 */
	public function create() : Control;
}
