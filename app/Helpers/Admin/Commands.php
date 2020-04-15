<?php


namespace App\Helpers\Admin;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class Commands
{
    /*
     * Запустить команду без консоли, возвращает ответ из консоли.
     * $command - php artisan писать не нужно! К примеру 'make:model Test'.
     */
    public static function getCommand($command)
    {
        if ($command) {
            try {
                $command = 'cd ' . base_path() . " && php artisan $command";
                $process = new Process($command);
                $process->run();
                if (!$process->isSuccessful()) {
                    Log::error('Error in try ' . __METHOD__);
                    return __('s.whoops');
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
