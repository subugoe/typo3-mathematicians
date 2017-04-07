<?php

namespace Subugoe\Mathematicians\Proxy;

interface ProxyInterface
{
    /**
     * @param string $term
     *
     * @return string
     */
    public function search($term);
}
