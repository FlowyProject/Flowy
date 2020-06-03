<?php
namespace flowy;

class StreamBuilder
{
    /** @var Stream */
    protected $stream;

    /** @var callable[] */
    protected $filters;

    public function __construct(Stream $stream)
    {
        if ($stream->isDisposed()) {
            throw new \RuntimeException("stream is disposed");
        }

        $this->stream = $stream;
        $this->filters = [];
    }

    public function filter(callable $filter): StreamBuilder
    {
        $this->addFilter($filter);
        return $this;
    }

    public function addFilter(callable $filter): void
    {
        $this->filters[] = $filter;
    }

    public function stream(): EventStream
    {
        $stream = new SubStream($this->stream, $this->filters);
        return new EventStream($stream);
    }
}