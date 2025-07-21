<?php

namespace N3XT0R\XPub\Contracts;

interface PublisherInterface {
    public function publish(string $title, string $content): bool;
}
