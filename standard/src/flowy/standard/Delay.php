<?php
namespace flowy\standard;

use pocketmine\scheduler\TaskScheduler;
use function flowy\listen;
use flowy\standard\delay\{DelayTask, DelayCallbackEvent};

function delay(TaskScheduler $scheduler, int $tick)
{
    $handler = $scheduler->scheduleDelayedTask(new DelayTask(), $tick);
    yield listen(DelayCallbackEvent::class)->filter(function($ev) use ($handler) {
        return $ev->getTaskId() === $handler->getTaskId();
    });
}
