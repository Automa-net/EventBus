<?php

namespace AutomaNet\EventBus\Driver\RabbitMq;

class RabbitMqConsumerConfig
{
    private string $queue;

    private bool $enableHeartbeatSender = false;

    private int $maxConsumeAttempts = 3;

    private int $nextAttemptDelay = 10;

    private int $prefetchCount = 1000;

    /**
     * @return string
     */
    public function getQueue(): string
    {
        return $this->queue;
    }

    /**
     * @return bool
     */
    public function isEnableHeartbeatSender(): bool
    {
        return $this->enableHeartbeatSender;
    }

    /**
     * @return int
     */
    public function getMaxConsumeAttempts(): int
    {
        return $this->maxConsumeAttempts;
    }

    /**
     * @return int
     */
    public function getNextAttemptDelay(): int
    {
        return $this->nextAttemptDelay;
    }

    /**
     * @return int
     */
    public function getPrefetchCount(): int
    {
        return $this->prefetchCount;
    }

    public static function fromArray(array $configData): self
    {
        $config = new RabbitMqConsumerConfig();

        if (!isset($configData['queue'])) {
            throw new \Exception('Queue is required parameter');
        }

        $config->queue = $configData['queue'];

        if (isset($configData['enable_heartbeat_sender'])) {
            $config->enableHeartbeatSender = $configData['enable_heartbeat_sender'];
        }

        if (isset($configData['max_consume_attempts'])) {
            $config->maxConsumeAttempts = intval($configData['max_consume_attempts']);
        }

        if (isset($configData['next_attempt_delay'])) {
            $config->nextAttemptDelay = intval($configData['next_attempt_delay']);
        }

        if (isset($configData['prefetch_count'])) {
            $config->prefetchCount = intval($configData['prefetch_count']);
        }

        return $config;
    }
}
