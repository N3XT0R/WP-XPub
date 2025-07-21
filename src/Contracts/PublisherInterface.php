<?php
namespace XPub\Contracts;

interface PublisherInterface {
    public function publish(string $title, string $content): bool;
}
