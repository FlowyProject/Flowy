<?php
namespace flowy;

use pocketmine\event\Event;

class RootStream extends Stream implements EventHandler
{
    /** @var EventListener */
    protected $eventListener;

    public function __construct(EventListener $eventListener)
    {
        $this->eventListener = $eventListener;
        $this->eventListener->setHandler($this);
        parent::__construct([]);
    }

    public function dispose(): void
    {
        parent::dispose();
        $this->eventListener->cancelAll();
    }

    public function handle(Event $event): void
    {
        $collector = new MatchedCollector($event);
        $collector->visitStream($this);
        $matched = $collector->getMatched();
        foreach ($matched as $coroutine) {
            $coroutine->onScheduled();
        }
        foreach ($matched as $coroutine) {
            if ($coroutine->isDisposed()) {
                continue;
            }
            if (!$coroutine->valid()) {
                continue;
            }
            $coroutine->continue($event);
        }
    }

    protected function listenEvent(string $event): void
    {
        if (isset($this->listening[$event])) {
            $this->listening[$event]++;
        }
        else {
            $this->listening[$event] = 1;
            $this->eventListener->listen($event);
        }
    }

    protected function cancelEvent(string $event): void
    {
        if (isset($this->listening[$event]) && 1 > --$this->listening[$event]) {
            unset($this->listening[$event]);
            $this->eventListener->cancel($event);
        }
    }
}