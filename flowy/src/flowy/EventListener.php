<?php
namespace flowy;

use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\event\RegisteredListener;

class EventListener implements Listener
{
    /** @var Plugin */
    protected $plugin;

    /** @var EventHandler */
    protected $handler;

    /** @var RegisteredListener[] */
    protected $registeredListeners;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        $this->handler = null;
        $this->registeredListeners = [];
    }

    public function setHandler(EventHandler $handler): void
    {
        $this->handler = $handler;
    }

    public function listen(string $event): void
    {
        if (isset($this->registeredListeners[$event])) {
            return;
        }

        $this->registeredListeners[$event] = EventHelper::register(
            $event,
            function($event) {
                $this->handler->handle($event);
            },
            EventPriority::NORMAL,
            $this->plugin
        );
    }

    public function cancel(string $event): void
    {
        if (isset($this->registeredListeners[$event])) {
            EventHelper::unregister($event, $this->registeredListeners[$event]);
            unset($this->registeredListeners[$event]);
        }
    }

    public function cancelAll(): void
    {
        foreach ($this->registeredListeners as $event => $listener) {
            EventHelper::unregister($event, $listener);
        }
        $this->registeredListeners = [];
    }
}