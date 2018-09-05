<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_mathematicians_pi1.php',
    '_pi1',
    'list_type',
    0
);

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['mathematicians_ow'] = 'EXT:'.$_EXTKEY.'/Classes/Proxy/OberwolfachProxy.php';
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['mathematicians_gen'] = 'EXT:'.$_EXTKEY.'/Classes/Proxy/GenealogyProxy.php';
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['mathematicians_mactut'] = 'EXT:'.$_EXTKEY.'/Classes/Proxy/MactutProxy.php';
