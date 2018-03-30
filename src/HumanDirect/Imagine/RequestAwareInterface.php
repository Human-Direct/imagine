<?php

namespace HumanDirect\Imagine;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestAwareInterface.
 */
interface RequestAwareInterface
{
    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void;
}
