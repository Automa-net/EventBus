<?php

namespace AutomaNet\EventBus\Examples\Fixtures\Event;

use AutomaNet\EventBus\Events\EventPayload;

class ProductUpdatedPayload extends EventPayload
{
    private int $id;

    private string $name;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}