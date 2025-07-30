<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Tests\Infrastructure\Wordpress\View;

use N3XT0R\XPub\Infrastructure\Wordpress\View\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir().'/xpub_view_test_'.uniqid();
        mkdir($this->tempDir, 0777, true);
        View::setBasePath($this->tempDir);
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->tempDir);
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }


    public function testRenderIncludesValidView(): void
    {
        $viewFile = $this->tempDir.'/example/view.php';
        mkdir(dirname($viewFile), 0777, true);
        file_put_contents($viewFile, '<?php echo "Hello $name";');

        ob_start();
        View::render('example.view', ['name' => 'World']);
        $output = ob_get_clean();

        $this->assertSame('Hello World', $output);
    }

    public function testPartialDelegatesToRender(): void
    {
        $viewFile = $this->tempDir.'/foo/bar.php';
        mkdir(dirname($viewFile), 0777, true);
        file_put_contents($viewFile, '<?php echo "Hi $name";');

        ob_start();
        View::partial('foo.bar', ['name' => 'Alice']);
        $output = ob_get_clean();

        $this->assertSame('Hi Alice', $output);
    }

    public function testSlotExecutesCallback(): void
    {
        ob_start();
        View::slot(function () {
            echo 'Content inside slot';
        });
        $output = ob_get_clean();

        $this->assertSame('Content inside slot', $output);
    }

    public function testRenderThrowsIfViewNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/not found/');

        View::render('nonexistent.view');
    }
}
