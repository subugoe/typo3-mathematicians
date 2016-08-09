<?php
namespace Subugoe\Mathematicians\Proxy;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class OberwolfachProxy
{

    public function main()
    {
        $owBaseURL = 'http://owpdb.mfo.de';
        $owSearchParam = 'term=';
        $owURL = $owBaseURL . '/vifa_search';
        $owSearchURL = 'http://owpdb.mfo.de/search?' . $owSearchParam;

        if (isset($_GET['person'])) {
            $params = GeneralUtility::_GET('person');

            $xml = simplexml_load_file($owURL . '?' . $owSearchParam . $params);

            $count = count($xml->result);
            $result = '<ul id="mycarousel" class="jcarousel-skin-tango owResult">';
            $showcount = 0;

            $images = [];
            if ($count > 0) {
                while (($showcount < $count) and ($showcount < 7)) {
                    $img = $owBaseURL . (string)$xml->result[$showcount]->thumbnail;

                    //extract person names
                    $names = '';
                    foreach ($xml->result[$showcount]->person as $name) {
                        $names .= ($names == '') ? $name : '&' . $name;
                    }
                    $link = $owBaseURL . (string)$xml->result[$showcount]->detail;

                    $images[$showcount] = '<a class="mathematicians_owsearch-link" target="_blank" href="' . $link . '"><img  class="mathematicians_owsearch-image"" src="' . $img . '" alt="photo: ' . $names . '" title="' . $names . '" height="110"/></a>';
                    $showcount++;
                }
                if ($count > 7) {
                    $images[$showcount] = '<a class="mathematicians_owsearch-link" target="_blank" href="' . $owSearchURL . $params . '">More...</a>';
                }
            } else {
                $images[0] = 'No photos found.';
            }
        }

        return '<div class="owResult">' . implode('', $images) . '</div>';
    }
}

/** @var \Subugoe\Mathematicians\Proxy\OberwolfachProxy $proxy */
$proxy = GeneralUtility::makeInstance(\Subugoe\Mathematicians\Proxy\OberwolfachProxy::class);
echo $proxy->main();
