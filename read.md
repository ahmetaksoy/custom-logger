config/logging.php

add channels

'eloquent' => [
    'driver' => 'eloquent',
    'model' => \App\Modules\Base\Models\Log::class,
    'level' => 'debug'
],

\Illuminate\Support\Facades\Log::channel('eloquent')->info('Test Log');