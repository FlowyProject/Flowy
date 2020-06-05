<?php
namespace flowy\standard\delay;

use flowy\standard\delay\{DelayTask, DelayCallbackEvent};

class Delay
{
    /* dummy */
    public static function bootstrap(): void
    {
    }
}

function delay(TaskScheduler $scheduler, int $tick)
{
    $handler = $scheduler->scheduleDelayedTask(new DelayTask(), $tick);
    yield listen(DelayCallbackEvent::class)->filter(function($ev) use ($handler) {
        return $ev->getTaskId() === $handler->getTaskId();
    });
}
