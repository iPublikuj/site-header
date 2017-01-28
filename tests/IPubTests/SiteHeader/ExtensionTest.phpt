<?php
/**
 * Test: IPub\SiteHeader\Extension
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:SiteHeader!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           28.01.17
 */

declare(strict_types = 1);

namespace IPubTests\SiteHeader;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\SiteHeader;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * Registering site header extension tests
 *
 * @package        iPublikuj:SiteHeader!
 * @subpackage     Tests
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ExtensionTest extends Tester\TestCase
{
	public function testFunctional()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('siteHeader.header') instanceof SiteHeader\Components\IControl);
		Assert::true($dic->getService('siteHeader.configuration') instanceof SiteHeader\Configuration);
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer() : Nette\DI\Container
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		SiteHeader\DI\SiteHeaderExtension::register($config);

		$config->addConfig(__DIR__ . DS . 'files'. DS .'config.neon');

		return $config->createContainer();
	}
}

\run(new ExtensionTest());
