<?php
namespace flowy\standard\delay;

use pocketmine\event\Event;

class DelayCallbackEvent extends Event {
    protected $taskId;

    public function __construct(int $taskId) {
        $this->taskId = $taskId;
    }

    public function getTaskId(): int {
        return $this->taskId;
    }
}
