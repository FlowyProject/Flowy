<?php
namespace flowy;

class EventStream
{
    /** @var Stream */
    protected $stream;

    public function __construct(Stream $stream)
    {
        if ($stream->isDisposed()) {
            throw new \RuntimeException("stream is disposed");
        }

        $this->stream = $stream;
    }

    public function run(callable $flow, array $args = []): Flow
    {
        array_unshift($args, $this);
        $rawCoroutine = $flow(...$args);
        $coroutine = new Coroutine($this->stream, $rawCoroutine);
        return new Flow($coroutine);
    }

    public function filter(callable $predicate): StreamBuilder
    {
        return (new StreamBuilder($this->stream))->filter($predicate);
    }

    public function close(): void
    {
        $this->stream->dispose();
    }

    public function isClosed(): bool
    {
        return $this->stream->isDisposed();
    }
}
