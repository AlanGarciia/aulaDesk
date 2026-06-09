<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Total alumnes: ' . \App\Models\Alumne::count() . PHP_EOL;
foreach (\App\Models\Alumne::all() as $a) {
    echo '#' . $a->id . ' espai:' . $a->espai_id . ' | ' . $a->nom . ' | idalu: ' . $a->idalu . ' | slug: ' . $a->slug . PHP_EOL;
}