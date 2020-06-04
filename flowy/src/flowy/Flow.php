<?php
namespace flowy;

class Flow
{
    /** @var Coroutine */
    protected $coroutine;

    public function __construct(Coroutine $coroutine)
    {
        $this->coroutine = $coroutine;
    }

    public function getStream(): Stream
    {
        if ($this->coroutine->isDisposed()) {
            throw new \RuntimeException("coroutine is disposed");
        }

        return $this->coroutine->getParent();
    }

    public function stop(): void
    {
        $this->coroutine->dispose();
    }

    public function isDisposed(): bool
    {
        return $this->coroutine->isDisposed();
    }

    public function isRunning(): bool
    {
        return $this->coroutine->getState() === Coroutine::RUNNING
            || $this->coroutine->getState() === Coroutine::SCHEDULED;
    }
}
