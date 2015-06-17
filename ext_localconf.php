<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');
## Extending TypoScript from static template uid=43 to set up userdefined tag:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'editorcfg', '
	tt_content.CSS_editor.ch.tx_mathematicians_pi1 = < plugin.tx_mathematicians_pi1.CSS_editor
', 43);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_mathematicians_pi1.php', '_pi1', 'list_type', 0);

$TYPO3_CONF_VARS['FE']['eID_include']['mathematicians_ow'] = 'EXT:'. $_EXTKEY .'/Classes/Proxy/OberwolfachProxy.php';
$TYPO3_CONF_VARS['FE']['eID_include']['mathematicians_gen'] = 'EXT:'. $_EXTKEY .'/Classes/Proxy/GenealogyProxy.php';
$TYPO3_CONF_VARS['FE']['eID_include']['mathematicians_mactut'] = 'EXT:'. $_EXTKEY .'/Classes/Proxy/MactutProxy.php';
