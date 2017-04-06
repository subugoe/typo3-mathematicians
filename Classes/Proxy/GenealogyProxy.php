<?php
namespace Subugoe\Mathematicians\Proxy;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class GenealogyProxy
{

    /**
     * @param $url
     * @param $referer
     * @param $_data
     * @return array
     */
    public function postRequest($url, $referer, $_data)
    {

        // convert variables array to string:
        $data = [];
        foreach ($_data as $key => $value) {
            $data[] = $key . '=' . $value;
        }

        $data = implode('&', $data);
        // format --> test1=a&test2=b etc.

        // parse the given URL
        $url = parse_url($url);
        if ($url['scheme'] != 'http') {
            die('Only HTTP request are supported !');
        }

        // extract host and path:
        $host = $url['host'];
        $path = $url['path'];

        // open a socket connection on port 80
        $fp = fsockopen($host, 80);

        // send the request headers:
        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "Referer: $referer\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, 'Content-length: ' . strlen($data) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $data);

        $result = '';
        while (!feof($fp)) {
            // receive the results of the request
            $result .= fgets($fp, 128);
        }

        // close the socket connection:
        fclose($fp);

        // split the result header from the content
        $result = explode("\r\n\r\n", $result, 2);

        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';

        // return as array:
        return [$header, $content];
    }
}

/** @var \Subugoe\Mathematicians\Proxy\GenealogyProxy $genealogyProxy */
$genealogyProxy = GeneralUtility::makeInstance(GenealogyProxy::class);

// submit these variables to the server:
$name = GeneralUtility::_GET('name');

$data = [
    'searchTerms' => $name,
];

// send a request to example.com (referer = jonasjohn.de)
list($header, $content) = $genealogyProxy->postRequest(
    'http://genealogy.math.ndsu.nodak.edu/quickSearch.php',
    'http://134.76.160.80/math',
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
        '<a class="external-link" target="_blank" href="http://genealogy.math.ndsu.nodak.edu/id.php?id=',
        $content);
}

echo $content;
