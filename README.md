[![CircleCI](https://circleci.com/gh/exilesprx/rabbit-worker-one/tree/master.svg?style=svg)](https://circleci.com/gh/exilesprx/rabbit-worker-one/tree/master)

# Phalcon

A project to test concurrency and versioning with multiple AMQP consumers and Queue workers.

#### Technologies
- Phalcon
  - PHP framework
- RabbitMQ
  - Message bus used to consumer events from a publisher
- Beanstalkd 
  - Queue used to consumer jobs. Events received from RabbitMQ are pushed to this queue.
- MariaDB
  - Data store
- MongoDB
  - Event store (not currently used)