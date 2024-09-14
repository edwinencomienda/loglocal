<?php

namespace App\Livewire;

use Illuminate\Support\LazyCollection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class LogMain extends Component
{
    public string $title = 'Livewire';

    public array $logs = [];

    public string $projectPath = '';

    #[Url]
    public string $logFile = '';

    #[Url]
    public string $filterLog = '';

    public function updatedProjectPath()
    {
        cache()->forget('projectPath');
        cache()->rememberForever('projectPath', fn () => $this->projectPath);

        if (count($this->logFiles()) < 1) {
            $this->js('$wire.logFile = null');
        }
    }

    public function updatedFilterLog()
    {
        $this->getLogs();
    }

    public function mount()
    {
        if (cache()->has('projectPath')) {
            $this->projectPath = cache()->get('projectPath');
        }

        $this->getLogs();
    }

    public function logPath()
    {
        if (empty($this->logFile)) {
            return data_get($this->logFiles(), '0.path');
        }

        return $this->logFile;
    }

    #[Computed]
    public function logFiles()
    {
        return collect(glob("{$this->projectPath}/storage/logs/*.log"))
            ->sort()
            ->map(fn ($log) => [
                'filename' => basename($log),
                'path' => $log,
            ])
            ->toArray();
    }

    public function getLastLines($file, $lines = 500)
    {
        if (! file_exists($file)) {
            return false; // File does not exist
        }

        $handle = fopen($file, 'rb');
        if ($handle === false) {
            return false; // Cannot open file
        }

        $buffer = '';
        $line_count = 0;

        // Start from the end of the file
        fseek($handle, 0, SEEK_END);
        $filesize = ftell($handle);

        for ($pos = $filesize - 1; $pos >= 0; $pos--) {
            fseek($handle, $pos);
            $char = fgetc($handle);
            if ($char === "\n") {
                $line_count++;
                if ($line_count > $lines) {
                    break;
                }
            }
            $buffer = $char.$buffer;
        }

        fclose($handle);

        return $buffer;
    }

    public function getLogs()
    {
        $this->logs = [];

        if (! file_exists($this->logPath())) {
            return;
        }

        $groupedLogs = [];
        $groupedLogsIndex = -1;
        $pattern = '/^\[\d{4}-\d{2}-\d{2}/';

        $counter = 0;
        LazyCollection::make(function () use (&$counter, $pattern) {
            $handle = fopen($this->logPath(), 'r');

            while (($line = fgets($handle)) !== false) {
                yield $line;

                $newLog = preg_match($pattern, $line);
                if ($newLog) {
                    $counter++;
                }

                // if ($counter >= 3) {
                //     break;
                // }
            }
        })
            ->each(function ($lines) use (&$groupedLogs, &$groupedLogsIndex, $pattern) {
                $newLog = preg_match($pattern, $lines);
                if ($newLog) {
                    [$env, $type] = str($lines)
                        ->after(']')
                        ->before(':')
                        ->explode('.')
                        ->map(fn ($v) => trim($v));

                    $groupedLogsIndex++;
                    $groupedLogs[$groupedLogsIndex]['timestamp'] = str($lines)->betweenFirst('[', ']')->toString();
                    $groupedLogs[$groupedLogsIndex]['log'] = trim(str($lines)->after("{$type}:"));
                    $groupedLogs[$groupedLogsIndex]['env'] = $env;
                    $groupedLogs[$groupedLogsIndex]['type'] = $type;
                    $groupedLogs[$groupedLogsIndex]['lines'] = $lines;
                } else {
                    $groupedLogs[$groupedLogsIndex]['log'] .= "\n".trim($lines);
                }
            });

        $this->logs = collect($groupedLogs)
            ->when($this->filterLog, fn ($logs) => $logs->filter(fn ($log) => str($log['log'])->lower()->contains(strtolower($this->filterLog))))
            ->reverse()
            ->take(50)
            ->toArray();
    }

    public function getLastLineNumber($filePath)
    {
        $lineCount = 0;
        $handle = fopen($filePath, 'r');

        if ($handle) {
            while (fgets($handle) !== false) {
                $lineCount++;
            }

            fclose($handle);
        }

        return $lineCount;
    }

    public function render()
    {
        return view('livewire.log-main');
    }
}
