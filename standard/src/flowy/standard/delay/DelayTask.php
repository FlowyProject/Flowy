<?php
namespace flowy\standard\delay;

use pocketmine\scheduler\Task;

class DelayTask extends Task {
    public function onRun($currentTick) {
        (new DelayCallbackEvent($this->getTaskId()))->call();
    }
}
