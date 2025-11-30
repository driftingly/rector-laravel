<?php

namespace RectorLaravel\ValueObject;

use PhpParser\Node\Expr\ClassConstFetch;

final class ReplaceServiceContainerCallArg
{
    /**
     * @readonly
     * @var string|\PhpParser\Node\Expr\ClassConstFetch
     */
    private $oldService;
    /**
     * @readonly
     * @var string|\PhpParser\Node\Expr\ClassConstFetch
     */
    private $newService;
    /**
     * @param string|\PhpParser\Node\Expr\ClassConstFetch $oldService
     * @param string|\PhpParser\Node\Expr\ClassConstFetch $newService
     */
    public function __construct($oldService, $newService)
    {
        $this->oldService = $oldService;
        $this->newService = $newService;
    }

    /**
     * @return string|\PhpParser\Node\Expr\ClassConstFetch
     */
    public function getOldService()
    {
        return $this->oldService;
    }

    /**
     * @return string|\PhpParser\Node\Expr\ClassConstFetch
     */
    public function getNewService()
    {
        return $this->newService;
    }
}
