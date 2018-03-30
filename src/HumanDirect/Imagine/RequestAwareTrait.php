<?php

namespace HumanDirect\Imagine;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestAwareTrait.
 */
trait RequestAwareTrait
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
