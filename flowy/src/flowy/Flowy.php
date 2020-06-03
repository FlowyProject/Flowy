<?php
namespace flowy;

use pocketmine\plugin\Plugin;

class Flowy
{
    /** dummy */
    public static function load()
    {
    }
}

function start(Plugin $plugin): EventStream
{
    return new EventStream(new RootStream(new EventListener($plugin)));
}

function listen(string ...$events): Listen
{
    return new Listen($events);
}