<?php

namespace App\Processes;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\User;
use App\Providers\ServiceProvider;
use Phalcon\Di\FactoryDefault;

$di = new FactoryDefault();
$provider = new ServiceProvider();

$provider->register($di);

$user = User::findFirst(
    [
        'user_id' => 1
    ]
);

$maxVersion = $user->getVersion();

$emails = \App\Store\Email::find(
    [
        'conditions' => 'userId = 1',
        'order' => 'id ASC'
    ]
);

$emails->rewind();

$version = 1;

while ($emails->valid()) {

    $email = $emails->current();

    if ($email->getVersion() != $version) {
        printf("Versions are not in order @ %d", $version);
        echo "\n";
    }

    $emails->next();

    $version++;
}

if ($email->getVersion() != $maxVersion || $user->getVersion() != $maxVersion) {
    printf("Versions are not in order @ %d", $version);
    echo "\n";
}

// First Run: double consumer and single queue worker
//      Total:  7413 email records, perfect order,
//              7413 was the users last version

// Second Run: double consumer and double queue worker
//      Total: