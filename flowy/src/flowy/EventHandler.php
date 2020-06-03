<?php
namespace flowy;

use pocketmine\event\Event;

interface EventHandler
{
    public function handle(Event $event): void;
}