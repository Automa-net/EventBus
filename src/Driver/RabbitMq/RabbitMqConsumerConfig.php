<?php

namespace AutomaNet\EventBus\Driver\RabbitMq;

/**
 * @phpstan-type RabbitMqConsumerConfigArray array{
 *     driver: "rabbitmq",
 *     queue: string,
 *     enable_heartbeat_sender?: bool,
 *     prefetch_count?: int,
 *     consumer_tag?: string
 * }
 */
class RabbitMqConsumerConfig
{
    private string $queue;

    private string $consumerTag = '';

    private bool $enableHeartbeatSender = false;

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
    public function getPrefetchCount(): int
    {
        return $this->prefetchCount;
    }

    public function getConsumerTag(): string
    {
        return $this->consumerTag;
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

        return $config;
    }
}
