<?php
/**
 * Mount command for producer.
 *
 * PHP version 5
 *
 * @category ProducerCommand
 *
 * @author    Francesco Bianco <bianco@javanile.org>
 * @copyright 2015-2017 Javanile.org
 * @license   https://goo.gl/KPZ2qI  MIT License
 */

namespace Javanile\Webhook;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Manifest
{
    /**
     * @var bool|string
     */
    protected $manifest;

    /**
     * @var Logger
     */
    protected $errorLog;

    /**
     * @var Logger
     */
    protected $eventLog;

    /**
     * Manifest constructor.
     * @param null $manifest
     */
    public function __construct($manifest = null)
    {
        $this->basePath = realpath(__DIR__.'/../');

        //
        if (!$manifest) {
            $manifest = $this->basePath.'/manifest.json';
        }

        // generic error
        $this->errorLog = $this->buildLogger('error');
        $this->eventLog = $this->buildLogger('event');

        //
        if (!file_exists($manifest)) {
            return $this->errorLog->error('Manifest not found: '.$manifest);
        }

        //
        $this->manifest = realpath($manifest);
    }

    /**
     * @return mixed
     */
    public function loadManifest()
    {
        $manifest = json_decode(file_get_contents($this->manifest), true);

        if (!$manifest) {
            $this->errorLog->error('Manifest error: '.$this->getManifestError());
        }

        return $manifest;
    }

    /**
     * @param $manifest
     * @return bool|int|void
     */
    public function saveManifest($manifest)
    {
        if (!$manifest) {
            return $this->errorLog->error('Try to save empty manifeset.', debug_backtrace());
        }

        return file_put_contents(
            $this->manifest,
            json_encode(
                $manifest,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );
    }

    /**
     * @return bool
     */
    public function hasManifestError()
    {
        return json_last_error() !== JSON_ERROR_NONE;
    }

    /**
     * @return string
     */
    public function getManifestError()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return 'Empty manifest';

            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';

            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';

            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';

            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';

            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';

            default:
                return 'Unknown error';
        }
    }

    /**
     *
     */
    public function getTaskExec($task)
    {
        if (!$task) {
            return;
        }

        if (preg_match('/(^[a-z0-9-\/]+\.sh)/i', $task, $file)) {
            return 'chmod +x tasks/'.$file[1].'; ./tasks/'.$task;
        }

        return './tasks/'.$task;
    }

    /**
     *
     */
    protected function buildLogger($name, $level = null)
    {
        $level = $level !== null ? $level : Logger::INFO;

        $file = $this->basePath.'/log/'.$name.'.log';
        $logger = new Logger(strtoupper($name));
        $logger->pushHandler(new StreamHandler($name, $level));

        return $logger;
    }
}
