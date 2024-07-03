<?php

namespace RectorLaravel\Tests\Sets\Laravel90;


use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\MailManager;
use Illuminate\Mail\Message;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Testing\TestResponse;
use RectorLaravel\Tests\Sets\Laravel90\Source\Collection;

(new Collection())->reduceWithKeys(fn () => null);
(new Collection())->reduceMany(fn () => null);

(new Message())->getSwiftMessage();
(new Mailable())->withSwiftMessage();
(new MailMessage())->withSwiftMessage();
(new Mailer())->getSwiftMailer();
(new Mailer())->setSwiftMailer();
(new MailManager())->createTransport();

(new TestResponse())->assertDeleted(null);

?>
-----
<?php

namespace RectorLaravel\Tests\Sets\Laravel90;


use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\MailManager;
use Illuminate\Mail\Message;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Testing\TestResponse;
use RectorLaravel\Tests\Sets\Laravel90\Source\Collection;

(new Collection())->reduce(fn () => null);
(new Collection())->reduceSpread(fn () => null);

(new Message())->getSymfonyMessage();
(new Mailable())->withSymfonyMessage();
(new MailMessage())->withSymfonyMessage();
(new Mailer())->getSymfonyTransport();
(new Mailer())->setSymfonyTransport();
(new MailManager())->createSymfonyTransport();

(new TestResponse())->assertModelMissing(null);

?>
