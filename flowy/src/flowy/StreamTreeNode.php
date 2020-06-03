<?php
namespace flowy;

interface StreamTreeNode
{
    public function accept(StreamTreeVisitor $visitor): void;

    public function events(): array;

    public function dispose(): void;

    public function isDisposed(): bool;
}