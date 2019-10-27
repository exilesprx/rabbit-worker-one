<?php

use App\AggregateRoots\User;
use App\Entities\EmailValidation;
use App\Commands\UserUpdatedEmail;
use App\Exceptions\InvalidUpdateException;
use App\Exceptions\OutOfOrderException;
use App\Repositories\UserRepository;
use App\StateMachines\NewEmail;
use App\Tasks\TaskConductor;
use Codeception\Test\Unit;
use Faker\Factory;
use Phalcon\Di;
use Ramsey\Uuid\Uuid;

class UserAggregateRootTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /** @var \Faker\Generator */
    private $faker;

    /** @var User */
    private $user;

    private $conductor;

    protected function _before()
    {
        $di = Di::getDefault();
        $this->faker = Factory::create();

        $di->set(
            UserRepository::class,
            $this->makeEmpty(UserRepository::class)
        );

        $this->conductor = $this->makeEmpty(TaskConductor::class);

        $id = $this->faker->randomNumber();
        $email = $this->faker->email;

        $validation = new EmailValidation(1, $id, new NewEmail());

        $this->user = new User($id, $email, 1, $validation);
    }

    // tests
    public function testNewUserHasEmptyTasks()
    {
        $this->user->recordEvents($this->conductor);

        $this->tester->assertFalse($this->user->hasTasks(), "User tasks are not empty.");
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
        $command = $this->generateUserUpdateEmailCommand();
        $this->user->updateUserEmail($command);

        $this->tester->assertTrue($this->user->hasTasks(), "User doesn't have tasks when it should.");
    }

    public function testUserHasUpdateEmailAndVersionWithAPendingTask()
    {
        $command = $this->generateUserUpdateEmailCommand();
        $this->user->updateUserEmail($command);
        $data = $command->getData();

        $this->tester->assertTrue($this->user->hasTasks(), "User doesn't have tasks.");
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

        $this->tester->assertTrue($this->user->hasTasks(), "User doesn't have tasks.");
        $this->tester->assertEquals($thirdCommand->getData()['email'], $this->user->getEmail());
        $this->tester->assertEquals($thirdCommand->getData()['version'], $this->user->getVersion());
    }

    public function testTaskCollectionIsEmptyAfterRecordingEvents()
    {
        $firstCommand = $this->generateUserUpdateEmailCommand();
        $this->user->updateUserEmail($firstCommand);

        $this->user->recordEvents($this->conductor);
        $this->tester->assertFalse($this->user->hasTasks(), "User has tasks when it shouldn't.");
    }

    public function testUserHasInitialEmailValidationOfNew()
    {
        $this->tester->assertEquals('new', $this->user->getEmailStatus());
    }

    public function testUserHasEmailStatusOfInvalid()
    {
        $command = new UserUpdatedEmail(
            [
                'uuid' => Uuid::uuid4(),
                'payload' => [
                    'user_id' => $this->user->getId(),
                    'email' => "someinvalidemailaddress@test",
                    'version' => 2
                ]
            ]
        );

        $this->user->updateUserEmail($command);
        $this->user->recordEvents($this->conductor);

        $this->tester->assertFalse($this->user->isEmailValid());
    }

    public function testUserHasEmailStatusOfValid()
    {
        $command = $this->generateUserUpdateEmailCommand();
        $this->user->updateUserEmail($command);

        $this->tester->assertTrue($this->user->isEmailValid());
    }

    public function testSubEntityEventsAreFlushed()
    {
        $emailValidation = new EmailValidation(
            1,
            1,
            new NewEmail()
        );

        $user = new User(
            1,
            $this->faker->email,
            1,
            $emailValidation
        );

        $command = new UserUpdatedEmail(
            [
                'uuid' => Uuid::uuid4(),
                'payload' => [
                    'user_id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'version' => 2
                ]
            ]
        );

        $user->updateUserEmail($command);

        $user->recordEvents($this->conductor);

        $this->tester->assertFalse($emailValidation->hasTasks());

        $this->tester->assertFalse($user->hasTasks());
    }

    protected function generateUserUpdateEmailCommand(int $version = 2) : UserUpdatedEmail
    {
        return new UserUpdatedEmail(
            [
                'uuid' => Uuid::uuid4(),
                'payload' => [
                    'user_id' => $this->user->getId(),
                    'email' => $this->faker->email,
                    'version' => $version
                ]
            ]
        );
    }
}