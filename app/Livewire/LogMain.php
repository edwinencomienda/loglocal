<?php

namespace App\Livewire;

use Livewire\Component;

class LogMain extends Component
{
    public string $title = 'Livewire';

    public array $logs = [];

    public string $projectPath = '';

    public function updatedProjectPath()
    {
        cache()->forget('projectPath');
        cache()->rememberForever('projectPath', fn () => $this->projectPath);
    }

    public function mount()
    {
        if (cache()->has('projectPath')) {
            $this->projectPath = cache()->get('projectPath');
        }

        $this->getLogs();
    }

    public function getLogs()
    {
        $this->logs = [];

        if (! file_exists($this->projectPath.'/storage/logs/laravel.log')) {
            return;
        }

        $logs = file_get_contents($this->projectPath.'/storage/logs/laravel.log');
        $logs = explode("\n", $logs);

        $newLog = false;
        $groupedLogs = [];
        $groupedLogsIndex = -1;
        foreach ($logs as $log) {
            $pattern = '/^\[\d{4}-\d{2}-\d{2}/';
            $newLog = preg_match($pattern, $log);
            // sample start of a log '[2024-09-12 01:09:58] production.ERROR:' i want to split the timestamp, production and ERROR and put in the variable

            if ($newLog) {
                [$env, $type] = str($log)
                    ->after(']')
                    ->before(':')
                    ->explode('.')
                    ->map(fn ($v) => trim($v));

                $groupedLogsIndex++;
                $groupedLogs[$groupedLogsIndex]['timestamp'] = str($log)->betweenFirst('[', ']')->toString();
                $groupedLogs[$groupedLogsIndex]['log'] = trim(str($log)->after("{$type}:"));
                $groupedLogs[$groupedLogsIndex]['env'] = $env;
                $groupedLogs[$groupedLogsIndex]['type'] = $type;
                $newLog = false;
            } else {
                $groupedLogs[$groupedLogsIndex]['log'] .= "\n".trim($log);
            }
        }

        $this->logs = collect($groupedLogs)
            ->reverse()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.log-main');
    }
}
