<?php
namespace flowy;

use pocketmine\event\Event;

class Coroutine implements StreamTreeNode
{
    const UNINITIALIZED = 0;
    const LISTENING = 1;
    const SCHEDULED = 2;
    const RUNNING = 3;
    const DISPOSED = 4;

    /** @var Stream */
    protected $parent;

    /** @var \Generator */
    protected $rawCoroutine;

    /** @var int */
    protected $state;

    public function __construct(Stream $parent, \Generator $rawCoroutine)
    {
        if ($parent->isDisposed()) {
            throw new \RuntimeException("parent stream is disposed");
        }

        $this->parent = $parent;
        $this->rawCoroutine = $rawCoroutine;
        $this->state = self::UNINITIALIZED;

        if ($this->valid()) {
            $this->parent->add($this);
            $this->parent->listen($this);
            $this->state = self::LISTENING;
        }
        else {
            $this->dispose();
        }
    }

    public function valid(): bool
    {
        if ($this->rawCoroutine === null) {
            return false;
        }
        if (!$this->rawCoroutine->valid()) {
            return false;
        }
        if (!($this->rawCoroutine->current() instanceof Listen)) {
            return false;
        }
        return true;
    }

    public function dispose(): void
    {
        if ($this->isDisposed()) {
            return;
        }

        if ($this->state === self::UNINITIALIZED) {
            $this->parent->remove($this);
        }
        else if ($this->state === self::LISTENING) {
            $this->parent->cancel($this);
        }
        $this->parent = null;
        $this->rawCoroutine = null;
        $this->state = self::DISPOSED;
    }

    public function isDisposed(): bool
    {
        return $this->state == self::DISPOSED;
    }

    public function accept(StreamTreeVisitor $visitor): void
    {
        $visitor->visitCoroutine($this);
    }

    public function getParent(): Stream
    {
        if ($this->isDisposed()) {
            throw new \RuntimeException("object disposed");
        }

        return $this->parent;
    }

    /** @return string[] */
    public function events(): array
    {
        if ($this->isDisposed()) {
            throw new \RuntimeException("object disposed");
        }

        /** @var Listen $listen */
        $listen = $this->rawCoroutine->current();
        return $listen->getEvents();
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function match(Event $event): bool
    {
        if ($this->isDisposed()) {
            throw new \RuntimeException("object disposed");
        }
        if ($this->state !== self::LISTENING) {
            return false;
        }

        /** @var Listen $listen */
        $listen = $this->rawCoroutine->current();
        return $listen->match($event);
    }

    public function onScheduled(): void
    {
        $this->parent->cancel($this);
        $this->state = self::SCHEDULED;
    }

    public function continue(Event $event): void
    {
        $this->state = self::RUNNING;
        if ($this->valid()) {
            $this->rawCoroutine->send($event);
        }
        if ($this->valid()) {
            $this->parent->listen($this);
            $this->state = self::LISTENING;
        }
        else {
            $this->dispose();
        }
    }
}