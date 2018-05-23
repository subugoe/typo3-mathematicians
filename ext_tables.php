<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin([
    'LLL:EXT:mathematicians/locallang_db.xml:tt_content.list_type_pi1',
    $_EXTKEY.'_pi1',
], 'list_type');
