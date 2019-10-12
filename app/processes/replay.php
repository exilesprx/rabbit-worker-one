<?php
namespace App\Processes;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\User;
use App\Providers\ServiceProvider;
use Phalcon\Di\FactoryDefault;

$di = new FactoryDefault();
$provider = new ServiceProvider();

$provider->register($di);

$emails = \App\Store\Email::find(
    [
        'conditions' => 'userId = 1',
        'order' => 'id ASC'
    ]
);

$user = User::findFirst(
    [
        'user_id' => 1
    ]
);

$emails->rewind();

$state = [];

while ($emails->valid()) {

    $email = $emails->current();

    $state['email'] = $email->getEmail();
    $state['version'] = $email->getVersion();

    $user->assign($state);

    printf("State has been updated to %s", json_encode($state));
    echo "\n";

    $emails->next();
}

printf("The final state is %s", json_encode($state));
echo "\n";

$user->update();