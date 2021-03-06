<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */

namespace Infuse\Cron\Jobs;

use Infuse\Cron\Libs\Run;
use Infuse\HasApp;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * @codeCoverageIgnore
 */
class TestScheduledJob
{
    use HasApp;

    public function __invoke(Run $run)
    {
        // this is a sample cron job for testing purposes

        $t = date('h:i a');
        $subject = $this->app['config']->get('app.title').' Cron test: '.$t;
        $body = "This is a cron job test\n$t";

        // Create the Transport
        $transport = Swift_SmtpTransport::newInstance($this->app['config']->get('smtp.host'), $this->app['config']->get('smtp.port'))
          ->setUsername($this->app['config']->get('smtp.username'))
          ->setPassword($this->app['config']->get('smtp.password'));

        // Create the Mailer using your created Transport
        $mailer = Swift_Mailer::newInstance($transport);

        // Create a message
        $message = Swift_Message::newInstance($subject)
          ->setFrom([$this->app['config']->get('app.email') => $this->app['config']->get('app.title')])
          ->setTo([$this->app['config']->get('app.email') => $this->app['config']->get('app.title')])
          ->setBody(nl2br($body), 'text/html')
          ->addPart(strip_tags($body), 'text/plain');

        // sleep for 30 seconds to simulate a long-running cron job
        sleep(30);

        // send the e-mail
        return $mailer->send($message);
    }
}
