<?php

declare(strict_types=1);

namespace Subugoe\Mathematicians\Proxy;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class OberwolfachProxy implements ProxyInterface
{
    public function search(string $term): string
    {
        $owBaseURL = 'http://owpdb.mfo.de';
        $owSearchParam = 'term=';
        $owURL = $owBaseURL.'/vifa_search';
        $owSearchURL = 'http://owpdb.mfo.de/search?'.$owSearchParam;

        $xml = simplexml_load_file($owURL.'?'.$owSearchParam.$term);

        $count = count($xml->result);
        $showcount = 0;

        $images = [];
        if ($count > 0) {
            while (($showcount < $count) and ($showcount < 7)) {
                $img = $owBaseURL.(string) $xml->result[$showcount]->thumbnail;

                //extract person names
                $names = '';
                foreach ($xml->result[$showcount]->person as $name) {
                    $names .= ('' == $names) ? $name : '&'.$name;
                }
                $link = $owBaseURL.(string) $xml->result[$showcount]->detail;

                $images[$showcount] = '<a class="mathematicians_owsearch-link" target="_blank" href="'.$link.'"><img  class="mathematicians_owsearch-image"" src="'.$img.'" alt="photo: '.$names.'" title="'.$names.'" height="110"/></a>';
                ++$showcount;
            }
            if ($count > 7) {
                $images[$showcount] = '<a class="mathematicians_owsearch-link" target="_blank" href="'.$owSearchURL.$term.'">More...</a>';
            }
        } else {
            $images[0] = 'No photos found.';
        }

        return '<div class="ow-result">'.implode('', $images).'</div>';
    }
}

/** @var \Subugoe\Mathematicians\Proxy\OberwolfachProxy $proxy */
$proxy = GeneralUtility::makeInstance(\Subugoe\Mathematicians\Proxy\OberwolfachProxy::class);
echo $proxy->search(GeneralUtility::_GET('name'));
