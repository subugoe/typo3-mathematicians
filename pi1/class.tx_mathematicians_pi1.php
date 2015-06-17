<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 muehlhoelzer <mmuehlh@sub.uni-goettingen.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Plugin 'mathematicians' for the 'mathematicians' extension.
 */
class tx_mathematicians_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin {

	/**
	 * @var string
	 */
	public $prefixId = 'tx_mathematicians_pi1';

	/**
	 * @var string
	 */
	public $scriptRelPath = 'pi1/class.tx_mathematicians_pi1.php';

	/**
	 * @var string
	 */
	public $extKey = 'mathematicians';

	/**
	 * @var bool
	 */
	public $pi_checkCHash = TRUE;

	/**
	 * @var string
	 */
	protected $templateCode;

	/**
	 * @var \TYPO3\CMS\Fluid\View\StandaloneView
	 */
	protected $view;

	/**
	 * The main method of the PlugIn
	 *
	 * @param string $content The PlugIn content
	 * @param array $conf The PlugIn configuration
	 * @return string The content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;

		$this->addAssets();

		//generate search form
		if (isset($_POST['person'])) {
			$this->view = $this->initializeTemplate();
			$this->view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mathematicians') . 'Resources/Private/Templates/Search.html');
			$person = $_POST['person'];

			$this->view->assign('searchTerm', $person);
			$this->view->assign('owResult', $this->ow_search($person));
			$this->view->assign('genResult', $this->gen_search($person));
		} else {
			$this->view = $this->initializeTemplate();
			$this->view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mathematicians') . 'Resources/Private/Templates/Mathematicians.html');
		}

		$this->view->assign('language', $GLOBALS['TSFE']->sys_language_uid);

		return $this->view->render();
	}

	/**
	 * @return \TYPO3\CMS\Fluid\View\StandaloneView
	 */
	protected function initializeTemplate() {
		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $template */
		$template = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
		return $template;
	}

	protected function addAssets() {
		/** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
		$pageRenderer = $GLOBALS['TSFE']->getPageRenderer();

		$pageRenderer->addJsFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('mathematicians') . 'Resources/Public/JavaScript/jcarousel/lib/jquery.jcarousel.js');
		$pageRenderer->addJsFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('mathematicians') . 'Resources/Public/JavaScript/maths.js');

		$pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('mathematicians') . 'Resources/Public/Css/Mathematicians.css');
		$pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('mathematicians') . 'Resources/Public/JavaScript/jcarousel/lib/jquery.jcarousel.css');
		$pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('mathematicians') . 'Resources/Public/JavaScript/jcarousel/skins/ie7/skin.css');
	}

	/**
	 * searches in the Oberwolfach Photo Collection
	 *
	 * @param string $term : search term
	 * @return string $result: result link list
	 */
	protected function ow_search($term) {
		$owBaseURL = 'http://owpdb.mfo.de';
		$owURL = $owBaseURL . '/vifa_search';
		$owSearchParam = 'term=';

		$owSearchURL = $owBaseURL . '/search?' . $owSearchParam;

		$xml = simplexml_load_file($owURL . '?' . $owSearchParam . $term);
		$count = count($xml->result);
		$showcount = 0;

		$images = '';
		if ($count > 0) {

			while (($showcount < $count) and ($showcount < 7)) {
				$img = $owBaseURL . (string)$xml->result[$showcount]->thumbnail;

				//extract person names
				$names = "";
				foreach ($xml->result[$showcount]->person as $name) {
					$names .= ($names == "") ? $name : '&' . $name;
				}
				$link = $owBaseURL . (string)$xml->result[$showcount]->detail;

				$images .= '<a target="_blank" href="' . $link . '"><img  src="' . $img . '" alt="photo: ' . $names . '" title="' . $names . '" height="130"/></a>';
				$showcount++;
			}
			if ($count > 7) {

				$images .= '<a target="_blank" href="' . $owSearchURL . '">More...</a>';
			}
		} else {
			$images = 'No photos found';
		}
		return $images;
	}

	/**
	 * searches in the Genealogy DB Bielefeld
	 *
	 * @param string $term search term
	 * @return string $result result link list
	 */
	protected function gen_search($term) {

		$data = array(
			'searchTerms' => $term,
		);

		// send a request to example.com (referer = jonasjohn.de)
		list($header, $content) = $this->PostRequest(
			"http://genealogy.math.uni-bielefeld.de/genealogy/quickSearch.php",
			"http://134.76.160.80/math",
			$data
		);

		// print the result of the whole request:
		$start = strpos($content, '<table');
		if (strpos($content, 'Your search has found') < 0) {
			$content = '';
		} else {
			$end = strpos($content, '</table>');
			$offset = $end - $start + 8;
			$content = substr($content, $start, $offset);
			$content = str_replace('<a href="id.php?id=', '<a class="external-link" target="_blank" href="http://genealogy.math.uni-bielefeld.de/genealogy/id.php?id=', $content);
		}

		return $content;

	}

	/**
	 * @param $url
	 * @param $referer
	 * @param $_data
	 * @return array
	 */
	protected function PostRequest($url, $referer, $_data) {

		// convert variables array to string:
		$data = array();
		while (list($n, $v) = each($_data)) {
			$data[] = "$n=$v";
		}
		$data = implode('&', $data);

		// parse the given URL
		$url = parse_url($url);
		if ($url['scheme'] != 'http') {
			die('Only HTTP request are supported !');
		}

		// extract host and path:
		$host = $url['host'];
		$path = $url['path'];

		// open a socket connection on port 80
		$fp = fsockopen($host, 80);

		// send the request headers:
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Referer: $referer\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: " . strlen($data) . "\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data);

		$result = '';
		while (!feof($fp)) {
			// receive the results of the request
			$result .= fgets($fp, 128);
		}

		// close the socket connection:
		fclose($fp);

		// split the result header from the content
		$result = explode("\r\n\r\n", $result, 2);

		$header = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';

		// return as array:
		return array($header, $content);
	}

	/**
	 * searches in the file mactut.txt for links containing the search term
	 *
	 * @param string $term : search term
	 * @return string $result: result link list
	 */
	protected function mactut_search($term) {

		$term = str_replace(',', '', $term);

		$idx = strpos($term, ' ');
		if ($idx > 0) {
			$term = substr($term, 0, $idx);
		}
		$text = file_get_contents(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mathematicans') . 'Resources/Private/Data/mactut.txt');
		$result = '';
		if (preg_match_all("/.*$term.*/i", $text, $matches)) {
			foreach ($matches[0] as $hit) {
				$result .= $hit . '<br />';
			}
		} else {
			$result = "No match found.";
		}
		return $result;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php']);
}
