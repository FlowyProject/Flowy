<?php
namespace flowy\standard;

use pocketmine\scheduler\TaskScheduler;
use flowy\Flowy;
use function flowy\listen;
use flowy\standard\delay\Delay;

class Standard
{
    /* dummy */
    public static function bootstrap(): void
    {
        Delay::bootstrap();
    }
}
