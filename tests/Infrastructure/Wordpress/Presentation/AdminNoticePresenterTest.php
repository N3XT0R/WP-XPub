<?php

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\Presentation;

use N3XT0R\XPub\Infrastructure\Wordpress\Presentation\AdminNoticePresenter;
use N3XT0R\XPub\Domain\Settings\SettingsRepositoryInterface;
use PHPUnit\Framework\TestCase;

class AdminNoticePresenterTest extends TestCase
{
    private function repoWithMessage(string $msg): SettingsRepositoryInterface
    {
        return new class($msg) implements SettingsRepositoryInterface {
            private array $data;
            public function __construct(string $msg) { $this->data['xpub_admin_notice'] = $msg; }
            public function get(string $key, mixed $default = null): mixed { return $this->data[$key] ?? $default; }
            public function set(string $key, mixed $value): bool { $this->data[$key] = $value; return true; }
            public function delete(string $key): bool { unset($this->data[$key]); return true; }
        };
    }

    public function testShowOutputsNoticeAndDeletesOption(): void
    {
        $repo = $this->repoWithMessage('hello');
        $presenter = new AdminNoticePresenter($repo);

        ob_start();
        $presenter->showIfAvailable();
        $output = ob_get_clean();

        $this->assertStringContainsString('hello', $output);
        $this->assertNull($repo->get('xpub_admin_notice'));
    }
}
