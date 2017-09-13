<?php
/**
 * Command line tool for vendor code.
 *
 * PHP version 5
 *
 * @category  CommandLine
 * @author    Francesco Bianco <bianco@javanile.org>
 * @license   https://goo.gl/KPZ2qI  MIT License
 * @copyright 2015-2017 Javanile.org
 */

require_once __DIR__.'/vendor/autoload.php';

use Javanile\Webhook\Endpoint as WebhookEndpoint;

$manifest = __DIR__.'/manifest.json';

$endpoint = new WebhookEndpoint([
    'manifest' => $manifest,
    'input'    => 'php://input',
    'hook'     => filter_input(INPUT_GET, 'hook'),
]);

echo $endpoint->run();
