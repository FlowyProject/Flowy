<?php
namespace flowy;

interface StreamTreeVisitor
{
    public function visitStream(Stream $stream): void;

    public function visitCoroutine(Coroutine $coroutine): void;
}