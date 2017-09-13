<?php
/**
 * Class MainPrivateApplication.
 */

namespace Javanile\Webhook;

use Yalesov\CronExprParser\Parser;

class Tools extends Manifest
{
    public function runCronInit()
    {
        $manifest = $this->loadManifest();
        $needsave = false;

        foreach ($manifest['cron'] as $cron) {
            $time = $cron['time'];
            if (Parser::matchTime('now', $time)) {
                $needsave = true;
                $task = $cron['task'];
                $manifest['once'][] = $task;
            }
        }

        if ($needsave) {
            $this->saveManifest($manifest);
        }
    }

    public function runCronFeed()
    {
        $manifest = $this->loadManifest();

        if (!isset($manifest['once']) || !$manifest['once']) {
            return;
        }

        $task = array_pop($manifest['once']);

        if (!$manifest['once']) {
            unset($manifest['once']);
        }

        $manifest['skip'][] = $task;

        $this->saveManifest($manifest);

        return $task;
    }

    public function runCronDone()
    {
        $manifest = $this->loadManifest();
        unset($manifest['skip']);
        $this->saveManifest($manifest);
    }

    /**
     * Get remote url for call webhook.
     *
     * @param mixed $url
     */
    public function getRemoteUrl($url)
    {

        //$parts = parse_url($url);

        return $url.'/webhook.php';
    }

    /**
     * Get remote data for call webhook.
     */
    public function getRemoteData()
    {
        $data = [
            'ref' => 'refs/heads/master',
        ];

        return json_encode($data);
    }
}
