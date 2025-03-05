<?php
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SetDataEvent extends Event
{
    protected $events;

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function setEvents(array $events): self
    {
        $this->events = $events;
        return $this;
    }
}
