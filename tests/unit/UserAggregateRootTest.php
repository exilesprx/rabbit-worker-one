<?php

use App\AggregateRoots\User;
use App\Events\UserUpdatedEmail;
use App\Exceptions\InvalidUpdateException;
use App\Exceptions\OutOfOrderException;

class UserAggregateRootTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var \Faker\Generator */
    private $faker;

    /** @var User */
    private $user;

    protected function _before()
    {
        $this->faker = \Faker\Factory::create();

        $id = $this->faker->randomNumber();
        $email = $this->faker->email;

        $this->user = new User($id, $email, 1);
    }

    // tests
    public function testNewUserHasEmptyTasks()
    {
        $this->tester->assertTrue($this->user->getTasks()->isEmpty(), "User tasks are not empty.");
    }

    public function testUserCannotSkipAVersion()
    {
        $command = $this->generateUserUpdateEmailCommand(3);

        $this->tester->expectThrowable(OutOfOrderException::class, function() use($command) {
            $this->user->updateUserEmail($command);
        });
    }

    public function testCannotUpdatePropertiesOfDifferentUserInstance()
    {
        $command = new UserUpdatedEmail(
            [
                'payload' => [
                    'user_id' => $this->user->getId() * 4,
                    'email' => $this->faker->email,
                    'version' => 2
                ]
            ]
        );

        $this->tester->expectThrowable(InvalidUpdateException::class, function() use($command) {
            $this->user->updateUserEmail($command);
        });
    }

    public function testUserHasATaskAfterUpdatingEmail()
    {
        $command = $this->generateUserUpdateEmailCommand(2);
        $this->user->updateUserEmail($command);

        $this->tester->assertTrue($this->user->getTasks()->hasTasks(), "User doesn't have tasks.");
        $this->tester->assertCount(1, $this->user->getTasks()->getTasks());
    }

    public function testUserHasUpdateEmailAndVersionWithAPendingTask()
    {
        $command = $this->generateUserUpdateEmailCommand(2);
        $this->user->updateUserEmail($command);
        $data = $command->getData();

        $this->tester->assertTrue($this->user->getTasks()->hasTasks(), "User doesn't have tasks.");
        $this->tester->assertCount(1, $this->user->getTasks()->getTasks());
        $this->tester->assertEquals($data['email'], $this->user->getEmail());
        $this->tester->assertEquals($data['version'], $this->user->getVersion());
    }

    public function testCanUpdateAUserMultipleTimesWithMultiplePendingTasks()
    {
        $firstCommand = $this->generateUserUpdateEmailCommand(2);
        $secondCommand = $this->generateUserUpdateEmailCommand(3);
        $thirdCommand = $this->generateUserUpdateEmailCommand(4);

        $this->user->updateUserEmail($firstCommand);
        $this->user->updateUserEmail($secondCommand);
        $this->user->updateUserEmail($thirdCommand);

        $this->tester->assertTrue($this->user->getTasks()->hasTasks(), "User doesn't have tasks.");
        $this->tester->assertCount(3, $this->user->getTasks()->getTasks());
        $this->tester->assertEquals($thirdCommand->getData()['email'], $this->user->getEmail());
        $this->tester->assertEquals($thirdCommand->getData()['version'], $this->user->getVersion());
    }

    protected function generateUserUpdateEmailCommand(int $version) : UserUpdatedEmail
    {
        return new UserUpdatedEmail(
            [
                'payload' => [
                    'user_id' => $this->user->getId(),
                    'email' => $this->faker->email,
                    'version' => $version
                ]
            ]
        );
    }
}