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
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
	
		//init
	
		$GLOBALS['TSFE']->additionalHeaderData[101] = '<script type="text/javascript" src="fileadmin/js/jcarousel/lib/jquery.jcarousel.pack.js"></script>';
                $GLOBALS['TSFE']->additionalHeaderData[102] = '<link rel="stylesheet" href="fileadmin/js/jcarousel//lib/jquery.jcarousel.css"></link>';
                $GLOBALS['TSFE']->additionalHeaderData[103] = '<link rel="stylesheet" href="fileadmin/js/jcarousel/skins/ie7/skin.css"></link>';
		
		$lang = $GLOBALS["TSFE"]->sys_language_uid;
		if ($lang == 1){
                $this->templateCode = $this->cObj->fileResource("EXT:mathematicians/pi1/template_en.htm");	
		$GLOBALS['TSFE']->additionalHeaderData[104] = '<script type="text/javascript" src="fileadmin/js/maths_en.js"></script>';
		}
		else {

		$this->templateCode = $this->cObj->fileResource("EXT:mathematicians/pi1/template.htm");
		$GLOBALS['TSFE']->additionalHeaderData[104] = '<script type="text/javascript" src="fileadmin/js/maths.js"></script>';
		}

                $template = array();
                $template = $this->cObj->getSubpart($this->templateCode, $templateMarker);
                $markerArray = array();
                $markerArray["###OWLOGO###"] = 'fileadmin/images/ow-logo.gif';
                $markerArray['###GENLOGO###']= 'fileadmin/images/gen-logo.gif';
                $markerArray["###MACTUTLOGO###"] = 'fileadmin/images/MTl-logo.gif';

		//init end 

		//generate search form
		$content='
	
			<form id="simpleSearch" action="/mathematicians" method="POST">
			<div class="first">
				<br />
				<fieldset>
				<input type="hidden" name="L" value="'. $lang .  '"/>
				<input class="iputStart" type="text" name="name" value=""/>
				<input class="submStart" type="submit" name="persSearch" value="'.htmlspecialchars($this->pi_getLL('submit_button_label')).'"/>
				</fieldset>
			</div>
			</form>
			<div class="clear2" />
		';

                if (isset($_POST['person'])) {
		$term = $_POST['person'];
		$templateMarker = "###TEMPLATE_RESULT###";

		
		//search OW
		$markerArray["###OWRESULT###"] = searchi_ow($term);


		//search Genealogy
		$markerArray["###GENRESULT###"] = search_gen($term);

		//search MacTutor
		$markerArray["###MACTUTRESULT###"] = search_mactut($term);
                }
                else {
		//write intro text
		$templateMarker = "###TEMPLATE###";
		}
		$content .= $this->cObj->substituteMarkerArrayCached($template, array(), $markerArray , array());
		return $this->pi_wrapInBaseClass($content);
	}
}


function search_ow($person) {
$owBaseURL = 'http://owpdb.mfo.de';
$owURL = $owBaseURL . '/vifa_search';
$owSearchParam = 'term=';

$owSearchURL = $owBaseURL . '/search?' . $owSearchParam;


$params = $person;
$xml = simplexml_load_file($owURL. '?'. $owSearchParam . $params);
$count = count($xml->result);

$images = '';
$text = "No photos found.";
  if ($count > 0) {

        while (($showcount < $count) and ($showcount < 7)) {
                $img = $owBaseURL . (string)$xml->result[$showcount]->thumbnail;

                //extract person names
                $names = "";
                foreach ($xml->result[$showcount]->person as $name) {

                                $names .= ($names == "")?$name:'&' . $name;

                }
                $link = $owBaseURL . (string)$xml->result[$showcount]->detail;

                $images .= '<a target="_blank" href="' . $link .'"><img  src="' . $img . '" alt="photo: ' . $names . '" title="' . $names . '" height="130"/></a>]]></link>';
                $showcount++;
       }
       if ($count > 7) {

                $images .= '<a target="_blank" href="'. $owSearchURL . $params .'">More...</a>';
        }
  }

return $images;

}


function search_gen($person) {

}

function search_mactut($person) {

$result='';

$links = file_get_contents('mactut.txt');
    if (preg_match_all("/.*$term.*/i",$links, $matches )) {
        foreach ($matches[0] as  $hit) {

                $result .=  $hit . '<br />';
        }
    } else 
    {
        $result =  "A match was not found.";
     }
return $result;
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php']);
}

?>
