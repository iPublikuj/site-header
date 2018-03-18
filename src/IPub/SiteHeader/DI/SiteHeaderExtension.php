<?php
/**
 * SiteHeaderExtension.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:SiteHeader!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           28.01.17
 */

declare(strict_types = 1);

namespace IPub\SiteHeader\DI;

use Nette;
use Nette\DI;
use Nette\PhpGenerator as Code;

use IPub;
use IPub\SiteHeader;
use IPub\SiteHeader\Components;

/**
 * Site header extension container
 *
 * @package        iPublikuj:SiteHeader!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class SiteHeaderExtension extends DI\CompilerExtension
{
	/**
	 * @return void
	 */
	public function loadConfiguration() : void
	{
		// Get container builder
		$builder = $this->getContainerBuilder();

		// Define component
		$builder->addDefinition($this->prefix('header'))
			->setClass(Components\Control::class)
			->setImplement(Components\IControl::class)
			->setInject(TRUE)
			->addTag('cms.components');

		// Header configurator
		$builder->addDefinition($this->prefix('configuration'))
			->setClass(SiteHeader\Configuration::class);
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(Nette\Configurator $config, string $extensionName = 'siteHeader') : void
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new SiteHeaderExtension());
		};
	}
}
