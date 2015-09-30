<?php

/**
 * @author Jared King <j@jaredtking.com>
 *
 * @link http://jaredtking.com
 *
 * @copyright 2015 Jared King
 * @license MIT
 */
namespace app\cron\libs;

use app\cron\models\CronJob;

class Cron
{
    /**
     * Checks the cron schedule and runs tasks.
     *
     * @param bool $echoOutput echoes output
     *
     * @return bool true if all tasks ran successfully
     */
    public static function scheduleCheck($echoOutput = false)
    {
        if ($echoOutput) {
            echo "-- Starting Cron\n";
        }

        $success = true;

        foreach (CronJob::overdueJobs() as $jobInfo) {
            $job = $jobInfo['model'];

            if ($echoOutput) {
                echo "-- Starting {$job->module}.{$job->command}:\n";
            }

            $result = $job->run($jobInfo['expires'], $jobInfo['successUrl']);
            $output = $job->last_run_output;

            if ($echoOutput) {
                if ($result == CRON_JOB_LOCKED) {
                    echo "{$job->module}.{$job->command} locked!\n";
                } elseif ($result == CRON_JOB_CONTROLLER_NON_EXISTENT) {
                    echo "{$job->module} does not exist\n";
                } elseif ($result == CRON_JOB_METHOD_NON_EXISTENT) {
                    echo "{$job->module}\-\>{$job->command}() does not exist\n";
                } elseif ($result == CRON_JOB_FAILED) {
                    if ($output) {
                        echo "$output\n";
                    }
                    echo "-- Failed!\n";
                } elseif ($result == CRON_JOB_SUCCESS) {
                    if ($output) {
                        echo "$output\n";
                    }
                    echo "-- Success!\n";
                }
            }

            $success = $result == CRON_JOB_SUCCESS && $success;
        }

        return $success;
    }
}
