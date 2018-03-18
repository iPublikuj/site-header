<?php
/**
 * Configuration.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikujSiteHeader!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           28.01.17
 */

namespace IPub\SiteHeader;

use Nette;
use Nette\Http;
use Nette\Localization;
use Nette\Utils;

use IPub\SiteHeader\Exceptions;

/**
 * Site header configuration
 *
 * @package        iPublikujSiteHeader!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Configuration
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * Content types
	 */
	public const CONTENT_TYPE_TEXT_HTML = 'text/html';
	public const CONTENT_TYPE_APPLICATION_XHTML = 'application/xhtml+xml';

	/**
	 * Doctypes
	 */
	public const HTML_4 = self::HTML_4_STRICT; // Backwards compatibility
	public const HTML_4_STRICT = 'html4_strict';
	public const HTML_4_TRANSITIONAL = 'html4_transitional';
	public const HTML_4_FRAMESET = 'html4_frameset';

	public const HTML_5 = 'html5';

	public const XHTML_1 = self::XHTML_1_STRICT; // Backwards compatibility
	public const XHTML_1_STRICT = 'xhtml1_strict';
	public const XHTML_1_TRANSITIONAL = 'xhtml1_transitional';
	public const XHTML_1_FRAMESET = 'xhtml1_frameset';

	/**
	 * @var string
	 */
	private $documentType = self::HTML_5;

	/**
	 * Document content type
	 *
	 * @var string
	 */
	private $contentType;

	/**
	 * Whether XML content type should be forced or not
	 *
	 * @var bool
	 */
	private $forceContentType;

	/**
	 * @var string
	 */
	private $language = 'en';

	/**
	 * @var array
	 */
	private $title = [];

	/**
	 * @var string
	 */
	private $titleSeparator = ' - ';

	/**
	 * @var bool
	 */
	private $titleInReverseOrder = TRUE;

	/**
	 * @var array
	 */
	private $metaTags = [];

	/**
	 * @var string
	 */
	private $favicon;

	/**
	 * @var array
	 */
	private $customLinks = [];

	/**
	 * Site RSS channels
	 *
	 * @var array
	 */
	private $rssChannels = [];

	/**
	 * @var Http\Request
	 */
	private $httpRequest;

	/**
	 * @param Http\Request $httpRequest
	 * @param Localization\ITranslator|NULL $translator
	 */
	public function __construct(
		Http\Request $httpRequest,
		Localization\ITranslator $translator = NULL
	) {
		$this->httpRequest = $httpRequest;

		if ($translator && method_exists($translator, 'getLocale')) {
			$this->setLanguage($translator->getLocale());
		}

		$this->setContentType(self::CONTENT_TYPE_TEXT_HTML);
	}

	/**
	 * @param string $contentType
	 * @param bool $force
	 *
	 * @return void
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setContentType(string $contentType, bool $force = FALSE) : void
	{
		if (
			$contentType === self::CONTENT_TYPE_APPLICATION_XHTML &&
			$this->documentType !== self::XHTML_1_STRICT &&
			$this->documentType != self::XHTML_1_TRANSITIONAL &&
			$this->documentType != self::XHTML_1_FRAMESET
		) {
			throw new Exceptions\InvalidArgumentException(sprintf('Cannot send "%s" type with non-XML doctype.', $contentType));
		}

		if (!in_array($contentType, [self::CONTENT_TYPE_TEXT_HTML, self::CONTENT_TYPE_APPLICATION_XHTML], TRUE)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Content type "%s" is not supported.', $contentType));
		}

		$this->contentType = $contentType;

		$this->forceContentType = $force;
	}

	/**
	 * Get site content type
	 *
	 * @return string
	 */
	public function getContentType() : string
	{
		if ($this->isXhtmlContent()) {
			return self::CONTENT_TYPE_APPLICATION_XHTML;
		}

		return self::CONTENT_TYPE_TEXT_HTML;
	}

	/**
	 * Set site document type
	 *
	 * @param string $documentType
	 *
	 * @return void
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setDocumentType(string $documentType) : void
	{
		if (!in_array($documentType, [
			self::HTML_4_STRICT, self::HTML_4_TRANSITIONAL, self::HTML_4_FRAMESET, self::HTML_5, self::XHTML_1_STRICT, self::XHTML_1_TRANSITIONAL, self::XHTML_1_FRAMESET
		], TRUE)
		) {
			throw new Exceptions\InvalidArgumentException(sprintf('Document type "%s" is not supported.', $documentType));
		}

		$this->documentType = $documentType;

		Utils\Html::$xhtml = (
			$documentType === self::XHTML_1_STRICT ||
			$documentType === self::XHTML_1_TRANSITIONAL ||
			$documentType === self::XHTML_1_FRAMESET
		);
	}

	/**
	 * @return string
	 */
	public function getDocumentType() : string
	{
		return $this->documentType;
	}

	/**
	 * Get html doctype in full HTML
	 *
	 * @return string
	 */
	public function getDocumentTypeTag() : string
	{
		switch ($this->documentType) {
			case self::HTML_4_STRICT:
				return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';

			case self::HTML_4_TRANSITIONAL:
				return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

			case self::HTML_4_FRAMESET:
				return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';

			case self::XHTML_1_STRICT:
				return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

			case self::XHTML_1_TRANSITIONAL:
				return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

			case self::XHTML_1_FRAMESET:
				return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';

			case self::HTML_5:
			default:
				return '<!DOCTYPE html>';
		}
	}

	/**
	 * @param string $language
	 *
	 * @return void
	 */
	public function setLanguage(string $language) : void
	{
		$this->language = $language;
	}

	/**
	 * @return string
	 */
	public function getLanguage() : string
	{
		return $this->language;
	}

	/**
	 * @param string|array $title
	 *
	 * @return void
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setTitle($title) : void
	{
		if (is_array($title)) {
			$titles = $title;

			$this->title = [];

			foreach ($titles as $title) {
				$this->addTitle($title);
			}

		} elseif (is_string($title)) {
			$this->title = [];

			$this->addTitle($title);
		}
	}

	/**
	 * @param string $title
	 *
	 * @return void
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function addTitle(string $title) : void
	{
		$title = trim($title);

		if ($title === '') {
			throw new Exceptions\InvalidArgumentException('Title must be non-empty string.');
		}

		$this->title[] = $title;
	}

	/**
	 * Get title pice on selected index
	 *
	 * @param int|NULL $index
	 *
	 * @return string|NULL
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function getTitle($index = NULL) : ?string
	{
		if ($index !== NULL) {
			if ($this->title === []) {
				return NULL;

			} elseif (isset($this->title[$index])) {
				return $this->title[$index];
			}

			throw new Exceptions\InvalidArgumentException('Title has no value on requested index position.');
		}

		if ($this->titleInReverseOrder) {
			$title = array_reverse($this->title);
		} else {
			$title = $this->title;
		}

		return $title === [] ? NULL : implode($this->titleSeparator, $title);
	}

	/**
	 * @param string $separator
	 *
	 * @return void
	 */
	public function setTitleSeparator(string $separator) : void
	{
		$this->titleSeparator = $separator;
	}

	/**
	 * @param bool $titleInReverseOrder
	 *
	 * @return void
	 */
	public function setTitleInReverseOrder(bool $titleInReverseOrder) : void
	{
		$this->titleInReverseOrder = $titleInReverseOrder;
	}

	/**
	 * @param array $metaTags
	 *
	 * @return void
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setMetaTags(array $metaTags) : void
	{
		foreach ($metaTags as $metaTag) {
			if (!isset($metaTag['name']) || !isset($metaTag['value'])) {
				throw new Exceptions\InvalidArgumentException('Provided meta tag structure is invalid. It has to be array of metatags with name & value.');
			}

			$this->addMetaTag($metaTag['name'], $metaTag['value']);
		}
	}

	/**
	 * @param string $name
	 * @param string $value
	 *
	 * @return void
	 */
	public function addMetaTag(string $name, string $value) : void
	{
		$this->metaTags[$name] = $value;
	}

	/**
	 * @param string $name
	 *
	 * @return string|NULL
	 */
	public function getMetaTag(string $name) : ?string
	{
		return isset($this->metaTags[$name]) ? $this->metaTags[$name] : NULL;
	}

	/**
	 * @return array
	 */
	public function getMetaTags() : array
	{
		return $this->metaTags;
	}

	/**
	 * @param string $filename
	 *
	 * @return void
	 */
	public function setFavicon(string $filename) : void
	{
		$this->favicon = $filename;
	}

	/**
	 * @return string
	 */
	public function getFavicon() : string
	{
		return $this->favicon;
	}

	/**
	 * @param array $attributes
	 *
	 * @return void
	 */
	public function addCustomLink(array $attributes) : void
	{
		$this->customLinks[] = $attributes;
	}

	/**
	 * @return array
	 */
	public function getCustomLinks() : array
	{
		return $this->customLinks;
	}

	/**
	 * @param string $author
	 *
	 * @return void
	 */
	public function setAuthor(string $author) : void
	{
		$this->addMetaTag('author', $author);
	}

	/**
	 * @return string|NULL
	 */
	public function getAuthor() : ?string
	{
		return $this->getMetaTag('author');
	}

	/**
	 * @param string $description
	 *
	 * @return void
	 */
	public function setDescription(string $description) : void
	{
		$this->addMetaTag('description', $description);
	}

	/**
	 * @return string|NULL
	 */
	public function getDescription() : ?string
	{
		return $this->getMetaTag('description');
	}

	/**
	 * @param array $keywords
	 *
	 * @return void
	 */
	public function setKeywords(array $keywords) : void
	{
		foreach ($keywords as $keyword) {
			$keyword = trim($keyword);

			$this->addKeyword($keyword);
		}
	}

	/**
	 * @param string $keyword
	 *
	 * @return void
	 */
	public function addKeyword(string $keyword) : void
	{
		$keywords = $this->getMetaTag('keywords');
		$keywords = $keywords === NULL ? [] : explode(',', $keywords);

		$keywords[] = $keyword;

		$keywords = array_unique($keywords);

		$this->addMetaTag('keywords', implode(',', $keywords));
	}

	/**
	 * @return string|NULL
	 */
	public function getKeywords() : ?string
	{
		return $this->getMetaTag('keywords');
	}

	/**
	 * @param string $robots
	 *
	 * @return void
	 */
	public function setRobots(string $robots) : void
	{
		$this->addMetaTag('robots', $robots);
	}

	/**
	 * @return string|NULL
	 */
	public function getRobots() : ?string
	{
		return $this->getMetaTag('robots');
	}

	/**
	 * @param array $rssChannels
	 *
	 * @return void
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function setRSSChannels(array $rssChannels) : void
	{
		foreach ($rssChannels as $rssChannel) {
			if (!isset($rssChannel['title']) || !isset($rssChannel['link'])) {
				throw new Exceptions\InvalidArgumentException('Provided rss channels structure is invalid. It has to be array of channels with title & link.');
			}

			$this->addRSSChannel($rssChannel['title'], $rssChannel['link']);
		}
	}

	/**
	 * @param string $title
	 * @param string $link
	 *
	 * @return void
	 */
	public function addRSSChannel(string $title, string $link) : void
	{
		$this->rssChannels[md5($title . $link)] = [
			'title' => $title,
			'link'  => $link,
		];
	}

	/**
	 * @return array
	 */
	public function getRSSChannels() : array
	{
		return $this->rssChannels;
	}

	/**
	 * @return bool
	 */
	private function isXhtmlContent() : bool
	{
		if (
			$this->documentType === self::XHTML_1_STRICT &&
			$this->contentType === self::CONTENT_TYPE_APPLICATION_XHTML &&
			($this->forceContentType || $this->isClientXhtmlCompatible())
		) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @return bool
	 */
	private function isClientXhtmlCompatible() : bool
	{
		return stristr($this->httpRequest->getHeader('Accept'), 'application/xhtml+xml') || $this->httpRequest->getHeader('Accept') == '*/*';
	}
}
