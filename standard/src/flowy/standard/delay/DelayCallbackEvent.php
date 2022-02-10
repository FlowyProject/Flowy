<?php
namespace flowy\standard\delay;

use pocketmine\event\Event;
use pocketmine\scheduler\Task;

class DelayCallbackEvent extends Event {
    protected $task;

    public function __construct(Task $task) {
        $this->task = $task;
    }

    public function getTask(): Task {
        return $this->task;
    }
}
