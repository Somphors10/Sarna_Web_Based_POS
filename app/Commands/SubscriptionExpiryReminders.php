<?php

namespace App\Commands;

use App\Libraries\SubscriptionExpiryNotifier;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Send Gmail expiry reminders (7 / 3 / 1 days + expired).
 * Run daily via cron, e.g. php spark subscriptions:expiry-reminders
 */
class SubscriptionExpiryReminders extends BaseCommand
{
    protected $group       = 'OSPOS';
    protected $name        = 'subscriptions:expiry-reminders';
    protected $description = 'Email shop owners when subscription is near expiry or expired.';
    protected $usage       = 'subscriptions:expiry-reminders';

    public function run(array $params): void
    {
        CLI::write('Sending subscription expiry reminders...', 'yellow');
        $stats = (new SubscriptionExpiryNotifier())->notifyAll();
        CLI::write(
            'Done. sent=' . $stats['sent']
            . ' skipped=' . $stats['skipped']
            . ' errors=' . $stats['errors'],
            $stats['errors'] > 0 ? 'red' : 'green'
        );
    }
}
