<?php
namespace flowy;

use pocketmine\event\Event;
use pocketmine\event\HandlerListManager;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginException;
use pocketmine\event\RegisteredListener;
use pocketmine\timings\TimingsHandler;
use pocketmine\utils\Utils;

final class EventHelper
{
    private function __construct()
    {
    }

    public static function register(
        string $event,
        \Closure $handler,
        int $priority,
        Plugin $plugin,
        bool $handleCancelled = false
    ): RegisteredListener {

        if(!is_subclass_of($event, Event::class)){
            throw new PluginException($event . " is not an Event");
        }

        $handlerName = Utils::getNiceClosureName($handler);

        if(!$plugin->isEnabled()){
            throw new PluginException("Plugin attempted to register event handler " . $handlerName . "() to event " . $event . " while not enabled");
        }

        $timings = new TimingsHandler("Plugin: " . $plugin->getDescription()->getFullName() . " Event: " . $handlerName . "(" . (new \ReflectionClass($event))->getShortName() . ")");

        $registeredListener = new RegisteredListener($handler, $priority, $plugin, $handleCancelled, $timings);
        HandlerListManager::global()->getListFor($event)->register($registeredListener);
        return $registeredListener;
    }

    public static function unregister(string $event, RegisteredListener $listener): void
    {
        HandlerListManager::global()->getListFor($event)->unregister($listener);
    }
}