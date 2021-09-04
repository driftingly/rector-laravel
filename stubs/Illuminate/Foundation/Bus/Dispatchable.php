<?php
namespace Illuminate\Foundation\Bus;

if (class_exists('Illuminate\Foundation\Bus\Dispatchable')) {
    return;
}
trait Dispatchable
{
    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param  array  $chain
     * @return \Illuminate\Foundation\Bus\PendingChain
     */
    public static function withChain($chain)
    {
    }
}
