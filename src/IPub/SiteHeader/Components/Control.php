<?php
/**
 * Control.php
 *
 * @copyright      More in license.md
 * @license        http://www.fastybird.com
 * @author         Adam Kadlec http://www.fastybird.com
 * @package        iPublikuj:SiteHeader!
 * @subpackage     Addons
 * @since          1.0.0
 *
 * @date           12.03.14
 */

namespace IPub\SiteHeader\Components;

use Nette;
use Nette\Application;
use Nette\Http;
use Nette\Utils;

use IPub;
use IPub\SiteHeader;

/**
 * Site header control
 *
 * @package        iPublikuj:SiteHeader!
 * @subpackage     Components
 */
final class Control extends Application\UI\Control
{
	/**
	 * @var SiteHeader\Configuration
	 */
	private $configuration;

	/**
	 * @var Application\LinkGenerator
	 */
	private $linkGenerator;

	/**
	 * @var Http\Response
	 */
	private $httpResponse;

	public function __construct(
		SiteHeader\Configuration $configuration,
		Application\LinkGenerator $linkGenerator,
		Http\Response $httpResponse
	) {
		parent::__construct();

		$this->configuration = $configuration;
		$this->linkGenerator = $linkGenerator;
		$this->httpResponse = $httpResponse;
	}

	/**
	 * @return void
	 */
	public function render()
	{
		$this->renderBegin();
		$this->renderRss();
		$this->renderEnd();
	}

	/**
	 * @return void
	 */
	public function renderBegin()
	{
		$contentType = $this->configuration->getContentType();

		if ($contentType === SiteHeader\Configuration::CONTENT_TYPE_APPLICATION_XHTML) {
			if (!headers_sent()) {
				$this->httpResponse->setHeader('Vary', 'Accept');
			}
		}

		if (!headers_sent()) {
			$this->httpResponse->setContentType($this->configuration->getContentType(), 'utf-8');
		}

		if ($contentType === SiteHeader\Configuration::CONTENT_TYPE_APPLICATION_XHTML) {
			echo "<?xml version='1.0' encoding='utf-8'?>", PHP_EOL;
		}

		echo $this->configuration->getDocumentTypeTag(), PHP_EOL;

		echo $this->getHtmlTag()->startTag(), PHP_EOL;

		echo Utils\Html::el('head')->startTag(), PHP_EOL;

		if ($this->configuration->getDocumentType() !== SiteHeader\Configuration::HTML_5) {
			$metaLanguage = Utils\Html::el('meta');
			$metaLanguage->addAttributes([
				'http-equiv' => 'Content-Language',
				'content'    => $this->configuration->getLanguage(),
			]);

			echo $metaLanguage, PHP_EOL;
		}

		$metaContentType = Utils\Html::el('meta');
		$metaContentType->addAttributes([
			'http-equiv' => 'Content-Type',
			'content'    => $contentType . '; charset=utf-8',
		]);

		echo $metaContentType, PHP_EOL;

		$title = Utils\Html::el('title');
		$title->setText($this->configuration->getTitle());

		echo $title, PHP_EOL;

		if ($this->configuration->getMetaTags()) {
			echo '<!-- Meta Tags -->', PHP_EOL;

			foreach ($this->configuration->getMetaTags() as $name => $content) {
				$metaCustom = Utils\Html::el('meta');
				$metaCustom->addAttributes([
					'name'    => $name,
					'content' => $content,
				]);

				echo $metaCustom, PHP_EOL;
			}
		}

		if ($this->configuration->getFavicon()) {
			echo '<!-- Favicon -->', PHP_EOL;

			$favicon = Utils\Html::el('link');
			$favicon->setAttribute('rel', 'shortcut icon');
			$favicon->href($this->configuration->getFavicon());

			echo $favicon, PHP_EOL;
		}

		if ($this->configuration->getCustomLinks()) {
			echo '<!-- Custom -->', PHP_EOL;

			foreach ($this->configuration->getCustomLinks() as $attributes) {
				$customLink = Utils\Html::el('link');
				$customLink->addAttributes($attributes);

				echo $customLink, PHP_EOL;
			}
		}
	}

	/**
	 * @return void
	 */
	public function renderEnd()
	{
		echo Utils\Html::el('head')->endTag();
	}

	/**
	 * @param array|NULL $channels
	 *
	 * @return void
	 *
	 * @throws Application\UI\InvalidLinkException
	 */
	public function renderRss(array $channels = NULL)
	{
		if ($channels === NULL || $channels === []) {
			$channels = $this->configuration->getRSSChannels();
		}

		foreach ($channels as $channel) {
			$link = Utils\Html::el('link');
			$link->addAttributes([
				'rel'   => 'alternate',
				'type'  => 'application/rss+xml',
				'title' => $channel['title'],
				'href'  => $this->linkGenerator->link($channel['link']),
			]);

			echo $link, PHP_EOL;
		}
	}

	/**
	 * Get html <html> tag
	 *
	 * @return Utils\Html
	 */
	private function getHtmlTag()
	{
		$html = Utils\Html::el('html');
		$html->addAttributes(['class' => 'uk-height-1-1']);

		if (Utils\Html::$xhtml) {
			$html->addAttributes([
				'xmlns'    => 'http://www.w3.org/1999/xhtml',
				'xml:lang' => $this->configuration->getLanguage(),
				'lang'     => $this->configuration->getLanguage(),
			]);

		} elseif ($this->configuration->getDocumentType() == SiteHeader\Configuration::HTML_5) {
			$html->addAttributes([
				'lang' => $this->configuration->getLanguage(),
			]);
		}

		return $html;
	}
}
