<?php

namespace RectorLaravel\Tests\Sets\ComposerBased\Fixture;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\PendingMail;

function sendMailNow(PendingMail $mail, Mailable $mailable)
{
    $mail->sendNow($mailable);
}

?>
