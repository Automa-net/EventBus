<?php

namespace AutomaNet\EventBus\Driver\RabbitMq;

/**
 * @phpstan-type RabbitMqConsumerConfigArray array{
 *     driver: "rabbitmq",
 *     queue: string,
 *     enable_heartbeat_sender?: bool,
 *     prefetch_count?: int,
 *     consumer_tag?: string,
 *     max_retries?: int,
 *     parking_lot_queue_name?: string,
 * }
 */
class RabbitMqConsumerConfig
{
    private string $queue;

    private string $consumerTag = '';

    private bool $enableHeartbeatSender = false;

    private int $prefetchCount = 1000;

    private int $maxRetries = 3;

    private ?string $parkingLotQueueName = null;

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
    public function getPrefetchCount(): int
    {
        return $this->prefetchCount;
    }

    public function getConsumerTag(): string
    {
        return $this->consumerTag;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function getParkingLotQueueName(): ?string
    {
        return $this->parkingLotQueueName;
    }

    /**
     * @param RabbitMqConsumerConfigArray $configData
     * @return self
     * @throws \Exception
     */
    public static function fromArray(array $configData): self
    {
        $config = new RabbitMqConsumerConfig();

        if (empty($configData['queue'])) {
            throw new \Exception('Queue is required parameter');
        }

        $config->queue = $configData['queue'];

        if (isset($configData['enable_heartbeat_sender'])) {
            $config->enableHeartbeatSender = $configData['enable_heartbeat_sender'];
        }

        if (isset($configData['prefetch_count'])) {
            $config->prefetchCount = intval($configData['prefetch_count']);
        }

        if (isset($configData['consumer_tag'])) {
            $config->consumerTag = strval($configData['consumer_tag']);
        }

        if (isset($configData['max_retries'])) {
            $config->maxRetries = intval($configData['max_retries']);
        }

        if (isset($configData['parking_lot_queue_name'])) {
            $config->parkingLotQueueName = $configData['parking_lot_queue_name'];
        }

        return $config;
    }
}
