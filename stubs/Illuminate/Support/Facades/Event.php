<?php

declare(strict_types=1);

namespace Illuminate\Support\Facades;

if (class_exists('\Illuminate\Support\Facades\Event')) {
    return;
}

require_once __DIR__ . '/Facade.php';

/**
 * @method static void listen(string|array $events, mixed $listener = null)
 * @method static bool hasListeners(string $eventName)
 * @method static array|null dispatch(string|object $event, mixed $payload = [], bool $halt = false)
 * @method static mixed until(string|object $event, mixed $payload = [])
 * @method static void push(string $event, array $payload = [])
 * @method static void flush(string $event)
 * @method static void forget(string $event)
 */
class Event extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'events';
    }
}
