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
		
	//	$GLOBALS['TSFE']->additionalHeaderData[100] = '<script type="text/javascript" src="fileadmin/js/jcarousel/lib/jquery-1.2.3.pack.js"></script>';
		$GLOBALS['TSFE']->additionalHeaderData[101] = '<script type="text/javascript" src="fileadmin/js/jcarousel/lib/jquery.jcarousel.pack.js"></script>';
                $GLOBALS['TSFE']->additionalHeaderData[102] = '<link rel="stylesheet" href="fileadmin/js/jcarousel//lib/jquery.jcarousel.css"></link>';
                $GLOBALS['TSFE']->additionalHeaderData[103] = '<link rel="stylesheet" href="fileadmin/js/jcarousel/skins/ie7/skin.css"></link>';
		$GLOBALS['TSFE']->additionalHeaderData[104] = '<script type="text/javascript" src="fileadmin/js/maths.js"></script>';		
		
		$lang = $GLOBALS["TSFE"]->sys_language_uid;
		if ($lang == 1){
                $this->templateCode = $this->cObj->fileResource("EXT:mathematicians/pi1/template_en.htm");	
		}
		else {

		$this->templateCode = $this->cObj->fileResource("EXT:mathematicians/pi1/template.htm");
		}

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
		//search OW

		//search Genealogy

		//search MacTutor
                }
                else {
		//write intro text
		$template = array();
		$templateMarker = "###TEMPLATE###";
		$template = $this->cObj->getSubpart($this->templateCode, $templateMarker);
		$markerArray = array();
		$markerArray["###OWLOGO###"] = 'fileadmin/images/ow-logo.gif';
		$markerArray['###GENLOGO###']= 'fileadmin/images/gen-logo.gif';
		$markerArray["###MACTUTLOGO###"] = 'fileadmin/images/MTl-logo.gif';
		$content .= $this->cObj->substituteMarkerArrayCached($template, array(), $markerArray , array());
		}
		return $this->pi_wrapInBaseClass($content);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mathematicians/pi1/class.tx_mathematicians_pi1.php']);
}

?>
