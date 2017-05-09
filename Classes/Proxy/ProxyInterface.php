<?php

declare(strict_types=1);

namespace Subugoe\Mathematicians\Proxy;

interface ProxyInterface
{
    /**
     * @param string $term
     *
     * @return string
     */
    public function search(string $term): string;
}
