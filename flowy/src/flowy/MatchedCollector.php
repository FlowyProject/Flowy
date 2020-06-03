<?php
namespace flowy;

use pocketmine\event\Event;

class MatchedCollector implements StreamTreeVisitor
{
    /** @var Event */
    protected $event;

    /** @var Coroutine[] */
    protected $matched;

    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->matched = [];
    }

    public function visitStream(Stream $stream): void
    {
        if ($stream->isDisposed()) {
            return;
        }
        if (!$stream->match($this->event)) {
            return;
        }

        foreach ($stream->getChildren() as $node) {
            $node->accept($this);
        }
    }

    public function visitCoroutine(Coroutine $coroutine): void
    {
        if ($coroutine->isDisposed()) {
            return;
        }
        if (!$coroutine->valid()) {
            return;
        }
        if (!$coroutine->match($this->event)) {
            return;
        }

        $this->matched[] = $coroutine;
    }

    /** @return Coroutine[] */
    public function getMatched(): array
    {
        return $this->matched;
    }
}