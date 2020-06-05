<?php
namespace flowy\standard;

use pocketmine\scheduler\TaskScheduler;
use flowy\Flowy;
use function flowy\listen;

class Standard
{
    /* dummy */
    public static function bootstrap(): void
    {
        Delay::bootstrap();
    }
}
