<?php

declare(strict_types=1);

namespace Subugoe\Mathematicians\Proxy;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MactutProxy implements ProxyInterface
{
    public function search(string $term): string
    {
        $term = str_replace(',', '', $term);
        $idx = strpos($term, ' ');
        if ($idx > 0) {
            $term = substr($term, 0, $idx);
        }
        $text = file_get_contents(ExtensionManagementUtility::extPath('mathematicans').'Resources/Private/Data/mactut.txt');

        $return = '';

        if (preg_match_all("/.*$term.*/i", $text, $matches)) {
            foreach ($matches[0] as $hit) {
                $return .= $hit.'<br />';
            }
        } else {
            $return = 'No match found.';
        }

        return $return;
    }
}

/** @var MactutProxy $proxy */
$proxy = GeneralUtility::makeInstance(MactutProxy::class);
echo $proxy->search(GeneralUtility::_GET('person'));
