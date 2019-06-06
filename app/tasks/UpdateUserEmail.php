<?php

namespace App\Tasks;

use App\models\User;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;

class UpdateUserEmail implements EventsAwareInterface
{
    protected $manager;

    /**
     * Sets the tasks manager
     *
     * @param ManagerInterface $eventsManager
     */
    public function setEventsManager(ManagerInterface $eventsManager)
    {
        $this->manager = $eventsManager;
    }

    /**
     * Returns the internal event manager
     *
     * @return ManagerInterface
     */
    public function getEventsManager(): ManagerInterface
    {
        return $this->manager;
    }

    public function execute(array $data)
    {
        $id = $data['payload']['user_id'];
        $email = $data['payload']['email'];
        $version = $data['payload']['version'];

        $user = User::findFirst(
            [
                'id' => $id
            ]
        );

        $user->update(
            [
                'email' => $email,
                'version' => $version
            ]
        );

        $this->manager->fire('user-update:emailUpdated', $this, $data);
    }
}