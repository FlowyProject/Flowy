<?php
namespace FlowyExamples\ListenExample;

use flowy\Flowy;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use function flowy\{listen, start};

class Main extends PluginBase
{
    function onEnable()
    {
        Flowy::bootstrap();
        $stream = start($this);
        $stream->run(function($stream) {
            while (true) {
                /** @var PlayerJoinEvent $event */
                $event = yield listen(PlayerJoinEvent::class);
                $event->getPlayer()->sendMessage("Welcome!!!");
            }
        });
    }
}
