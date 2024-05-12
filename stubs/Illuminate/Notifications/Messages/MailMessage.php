<?php

declare(strict_types=1);

namespace Illuminate\Notifications\Messages;

if (class_exists('Illuminate\Notifications\Messages\MailMessage')) {
    return;
}

class MailMessage
{
}
