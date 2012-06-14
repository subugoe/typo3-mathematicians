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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'mathematicians' for the 'mathematicians' extension.
 *
 * @author	muehlhoelzer <mmuehlh@sub.uni-goettingen.de>
 * @package	TYPO3
 * @subpackage	tx_mathematicians
 */
class tx_mathematicians_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_mathematicians_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_mathematicians_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'mathematicians';	// The extension key.
	var $pi_checkCHash = false;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf) {
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
	
		//init
		
		//init
		$GLOBALS['TSFE']->additionalHeaderData[101] = '<script type="text/javascript" src="fileadmin/js/jcarousel/lib/jquery.jcarousel.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData[102] = '<script type="text/javascript" src="fileadmin/js/maths.js"></script>';
                $GLOBALS['TSFE']->additionalHeaderData[103] = '<link rel="stylesheet" href="fileadmin/js/jcarousel//lib/jquery.jcarousel.css" />';
                $GLOBALS['TSFE']->additionalHeaderData[104] = '<link rel="stylesheet" href="fileadmin/js/jcarousel/skins/ie7/skin.css" />';
		
		$lang = $GLOBALS["TSFE"]->sys_language_uid;
		if ($lang == 1){
			$this->templateCode = $this->cObj->fileResource("EXT:mathematicians/pi1/template_en.htm");
		}
		else {
			$this->templateCode = $this->cObj->fileResource("EXT:mathematicians/pi1/template.htm");
		}
		$template = array();
        $markerArray = array();
		$markerArray["###LANG###"] = $lang;
        $markerArray["###OWLOGO###"] = 'fileadmin/images/ow-logo.gif';
        $markerArray['###GENLOGO###']= 'fileadmin/images/gen-logo.gif';
        //for later use
		//$markerArray["###MACTUTLOGO###"] = 'fileadmin/images/MTl-logo.gif';

		$content = '';
		//generate search form
        if (isset($_POST['person'])) {
			$person = $_POST['person'];
			$templateMarker = "###TEMPLATE_RESULT###";
            $template = $this->cObj->getSubpart($this->templateCode, $templateMarker);
			$markerArray["###SEARCHTERM###"] = $person;
			//search OW
			$markerArray["###OWRESULT###"]= ow_search($person);
			//search Genealogy
			$markerArray["###GENRESULT###"]= gen_search($person);

			//for later use:search MacTutor
			//$markerArray["###MACTUTRESULT###"]= mactut_search($person);
        }
        else {
			//write intro text
			$templateMarker = "###TEMPLATE###";
			$template = $this->cObj->getSubpart($this->templateCode, $templateMarker);
		}
		$content .= $this->cObj->substituteMarkerArrayCached($template, array(), $markerArray , array());
		return $this->pi_wrapInBaseClass($content);
	}
}

/**
* searches in the Oberwolfach Photo Collection
*
* @param string $term: search term
* @return string $result: result link list
*/

function ow_search($term) {
$owBaseURL = 'http://owpdb.mfo.de';
$owURL = $owBaseURL . '/vifa_search';
$owSearchParam = 'term=';

$owSearchURL = $owBaseURL . '/search?' . $owSearchParam;

$xml = simplexml_load_file($owURL. '?'. $owSearchParam . $term);
$count = count($xml->result);
$showcount = 0;

$images = '';
if ($count > 0) {

        while (($showcount < $count) and ($showcount < 7)) {
                $img = $owBaseURL . (string)$xml->result[$showcount]->thumbnail;

                //extract person names
                $names = "";
                foreach ($xml->result[$showcount]->person as $name) {

                                $names .= ($names == "")?$name:'&' . $name;

                }
                $link = $owBaseURL . (string)$xml->result[$showcount]->detail;

                $images .= '<a target="_blank" href="' . $link .'"><img  src="' . $img . '" alt="photo: ' . $names . '" title="' . $names . '" height="130"/></a>';
                //$images[$showcount] = '<img  src="'. $img . '" />';
                $showcount++;
       }
        if ($count > 7) {

                $images .= '<a target="_blank" href="'. $owSearchURL . $params .'">More...</a>';
        }
  }
  else {
        $images = 'No photos found';
  }
return $images;
}


function PostRequest($url, $referer, $_data) {

    // convert variables array to string:
    $data = array();
    while(list($n,$v) = each($_data)){
        $data[] = "$n=$v";
    }
    $data = implode('&', $data);
    // format --> test1=a&test2=b etc.

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
    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    fputs($fp, $data);

    $result = '';
    while(!feof($fp)) {
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
* searches in the Genealogy DB Bielefeld
*
* @param string $term: search term
* @return string $result: result link list
*/

function gen_search($term) {

$data = array(
    'searchTerms' => $term,
);

// send a request to example.com (referer = jonasjohn.de)
list($header, $content) = PostRequest(
    "http://genealogy.math.uni-bielefeld.de/genealogy/quickSearch.php",
    "http://134.76.160.80/math",
    $data
);

// print the result of the whole request:

    $start = strpos($content, '<table');
    if (strpos($content, 'Your search has found') < 0){
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
* searches in the file mactut.txt for links containing the search term
*
* @param string $term: search term
* @return string $result: result link list
*/

function mactut_search($term) {

	$term = str_replace(',', '', $term);

	$idx = strpos($term, ' ');
	if ($idx > 0) {
        	$term=substr($term, 0, $idx);

	}
	$text = file_get_contents('fileadmin/js/mactut.txt');
	$result = '';
	if (preg_match_all("/.*$term.*/i",$text, $matches )) {
        	foreach ($matches[0] as  $hit) {

               		$result .= $hit . '<br />';
        }
	} else {
    	$result = "No match found.";
	}
	return $result;
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php']);
}

?>
