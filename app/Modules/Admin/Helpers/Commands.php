<?php


namespace App\Modules\Admin\Helpers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class Commands
{
    /*
     * Запустить команду без консоли, возвращает ответ из консоли.
     * $command - php artisan писать не нужно! К примеру 'make:model Test'.
     * $params - .
     */
    public static function getCommand($command, $params = [])
    {
        if ($command) {
            try {
                $lang = lang();

                /*Artisan::call($command);
                return __("{$lang}::a.completed_successfully");*/

                $command = 'cd ' . base_path() . " && php artisan $command";
                $process = Process::fromShellCommandline($command);
                //$process = new Process($command);
                $process->run();
                if (!$process->isSuccessful()) {
                    Log::error('Error in try ' . __METHOD__);
                    return __("{$lang}::s.whoops");
                }
                return $process->getOutput();

            } catch (\Exception $e) {
                Log::error("Error {$e->getMessage()}. Error in catch " . __METHOD__);
                return $e->getMessage();
            }
        }
        return false;
    }
}
