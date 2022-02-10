<?php
namespace flowy\standard\delay;

use pocketmine\scheduler\Task;

class DelayTask extends Task {
    public function onRun(): void {
        (new DelayCallbackEvent($this))->call();
    }
}
