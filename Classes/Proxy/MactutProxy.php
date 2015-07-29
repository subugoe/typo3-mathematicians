<?php
namespace Subugoe\Mathematicians\Proxy;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class MactutProxy
{

    public function main()
    {
        $term = GeneralUtility::_GET('person');

        $term = str_replace(',', '', $term);
        $idx = strpos($term, ' ');
        if ($idx > 0) {
            $term = substr($term, 0, $idx);

        }
        $text = file_get_contents(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mathematicans') . 'Resources/Private/Data/mactut.txt');

        $return = '';

        if (preg_match_all("/.*$term.*/i", $text, $matches)) {
            foreach ($matches[0] as $hit) {
                $return .= $hit . '<br />';
            }
        } else {
            $return = 'No match found.';
        }

        return $return;
    }

}

/** @var MactutProxy $proxy */
$proxy = GeneralUtility::makeInstance(MactutProxy::class);

echo $proxy->main();
