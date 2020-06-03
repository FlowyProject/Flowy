<?php
namespace flowy;

use pocketmine\event\Event;

abstract class Stream implements StreamTreeNode
{
    /** @var callable[] */
    protected $filters;
    /** @var int[] */
    protected $listening;
    protected $disposed;
    /** @var \SplOBjectStorage */
    private $children;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        $this->children = new \SplObjectStorage;
        $this->listening = [];
        $this->disposed = false;
        if (($parent = $this->getParent()) !== null) {
            $parent->add($this);
        }
    }

    public function getParent(): ?Stream
    {
        return null;
    }

    public function add(StreamTreeNode $node): void
    {
        $this->children->attach($node);
    }

    public function accept(StreamTreeVisitor $visitor): void
    {
        $visitor->visitStream($this);
    }

    /** @return string[] */
    public function events(): array
    {
        return array_keys($this->listening);
    }

    public function dispose(): void
    {
        if ($this->isDisposed()) {
            return;
        }

        foreach ($this->children as $child) {
            if (!$child->isDisposed()) {
                $child->dispose();
            }
        }
        if (($parent = $this->getParent()) !== null) {
            $parent->remove($this);
        }
        $this->disposed = true;
    }

    public function isDisposed(): bool
    {
        return $this->disposed;
    }

    public function remove(StreamTreeNode $node): void
    {
        $this->children->detach($node);
    }

    public function match(Event $event): bool
    {
        foreach ($this->filters as $filter) {
            if ($filter($event) === false) {
                return false;
            }
        }
        return true;
    }

    /** @return StreamTreeNode[] */
    public function getChildren(): array
    {
        $children = [];
        foreach ($this->children as $child) {
            $children[] = $child;
        }
        return $children;
    }

    public function listen(StreamTreeNode $node): void
    {
        foreach ($node->events() as $event) {
            $this->listenEvent($event);
        }
    }

    protected function listenEvent(string $event): void
    {
        if (isset($this->listening[$event])) {
            $this->listening[$event]++;
        }
        else {
            $this->listening[$event] = 1;
            if (($parent = $this->getParent()) !== null) {
                $parent->listenEvent($event);
            }
        }
    }

    public function cancel(StreamTreeNode $node): void
    {
        foreach ($node->events() as $event) {
            $this->cancelEvent($event);
        }
    }

    protected function cancelEvent(string $event): void
    {
        if (isset($this->listening[$event]) && 1 > --$this->listening[$event]) {
            unset($this->listening[$event]);
            if (($parent = $this->getParent()) !== null) {
                $parent->cancelEvent($event);
            }
        }
    }

    protected function getRawChildren(): \SplObjectStorage
    {
        return $this->children;
    }
}