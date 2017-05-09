<?php

declare(strict_types=1);

namespace Subugoe\Mathematicians\Proxy;

use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GenealogyProxy implements ProxyInterface
{
    public function search(string $term): string
    {
        $data = [
            'searchTerms' => $term,
            'Submit' => 'Search',
        ];

        // send a request to example.com (referer = jonasjohn.de)
        $content = $this->postRequest(
            'https://www.genealogy.math.ndsu.nodak.edu/quickSearch.php',
            $data
        );

        // print the result of the whole request:
        $start = strpos($content, '<table');
        if (strpos($content, '0 records in our database') > 0) {
            $content = 'Sorry, no item found.';
        } else {
            $end = strpos($content, '</table>');
            $offset = $end - $start + 8;
            $content = substr($content, $start, $offset);
            $content = str_replace('<a href="id.php?id=',
                '<a class="external-link" target="_blank" href="https://genealogy.math.ndsu.nodak.edu/id.php?id=',
                $content);
        }

        return $content;
    }

    /**
     * @param string $url
     * @param array  $data
     *
     * @return string
     */
    private function postRequest($url, $data)
    {
        $content = '';

        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'allow_redirects' => true,
            'cookies' => false,
            'form_params' => $data,
        ];
        // Return a PSR-7 compliant response object
        $response = $requestFactory->request($url, 'POST', $additionalOptions);
        // Get the content as a string on a successful request
        if ($response->getStatusCode() === 200) {
            $content = $response->getBody()->getContents();
        }

        return $content;
    }
}

/** @var \Subugoe\Mathematicians\Proxy\GenealogyProxy $genealogyProxy */
$genealogyProxy = GeneralUtility::makeInstance(GenealogyProxy::class);
echo $genealogyProxy->search(GeneralUtility::_GET('name'));
