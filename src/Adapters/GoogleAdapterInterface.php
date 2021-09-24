<?php

namespace Teamones\Breaker\Adapters;

interface GoogleAdapterInterface
{

    public function collectionAdd(string $service): void;

    public function collectionReset(string $service): void;

    public function setFailure(string $service): void;

    public function setSuccess(string $service): void;

}