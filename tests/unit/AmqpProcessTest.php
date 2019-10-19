<?php

class AmqpProcessTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var \Phalcon\Di */
    protected $di;

    protected function _before()
    {
        $this->di = \Phalcon\Di\FactoryDefault::getDefault();
    }

    // tests
    public function testDefaultQueueTube()
    {
        $process = new \App\Helpers\AmqpProcess();

        $this->tester->assertEquals('default-tube', $process->getTube());
    }

    // tests
    public function testDefaultExchange()
    {
        $process = new \App\Helpers\AmqpProcess();

        $this->tester->assertEquals('default-exchange', $process->getExchange());
    }

    // tests
    public function testDefaultQueue()
    {
        $process = new \App\Helpers\AmqpProcess();

        $this->tester->assertEquals('default-queue', $process->getQueue());
    }
}