<?php

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['mathematicians_pi1'] = 'layout,select_key';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
    'LLL:EXT:mathematicians/locallang_db.xml:tt_content.list_type_pi1',
    'mathematicians_pi1',
    ],
    'list_type',
    'mathematicians'
);
