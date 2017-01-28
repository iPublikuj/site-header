<?php
/**
 * TSiteHeader.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:SiteHeader!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           01.02.15
 */

declare(strict_types = 1);

namespace IPub\SiteHeader;

use Nette;
use Nette\Application;

use IPub;
use IPub\SiteHeader\Components;

/**
 * Site header trait for presenters & components
 *
 * @package        iPublikuj:SiteHeader!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
trait TSiteHeader
{
	/**
	 * @var Components\IControl
	 */
	protected $siteHeaderFactory;

	/**
	 * @var Configuration
	 */
	protected $siteHeaderConfiguration;

	/**
	 * @param Components\IControl $siteHeaderFactory
	 * @param Configuration $configuration
	 */
	public function injectSiteHeader(
		Components\IControl $siteHeaderFactory,
		Configuration $configuration
	) {
		$this->siteHeaderFactory = $siteHeaderFactory;
		$this->siteHeaderConfiguration = $configuration;
	}

	/**
	 * @return Components\Control
	 */
	protected function createComponentSiteHeader() : Components\Control
	{
		return $this->siteHeaderFactory->create();
	}
}
