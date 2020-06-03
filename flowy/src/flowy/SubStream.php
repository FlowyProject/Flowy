<?php
namespace flowy;

class SubStream extends Stream
{
    /** @var Stream */
    protected $parent;

    public function __construct(Stream $parent, array $filters)
    {
        $this->parent = $parent;
        parent::__construct($filters);
    }

    public function getParent(): ?Stream
    {
        return $this->parent;
    }

    public function dispose(): void
    {
        parent::dispose();
        $this->parent = null;
    }
}