<?php
namespace N3XT0R\XPub\Application\Update;
function file_get_contents(string $filename, bool $use_include_path = false, $context = null)
{
    return \N3XT0R\XPub\Tests\Application\Update\ReleaseServiceTest::$content;
}
?>
<?php
namespace N3XT0R\XPub\Tests\Application\Update;

use N3XT0R\XPub\Application\Update\ReleaseService;
use PHPUnit\Framework\TestCase;

class ReleaseServiceTest extends TestCase
{
    public static string $content = '';

    public function testFetchLatestReleaseReturnsData(): void
    {
        self::$content = json_encode([
            'tag_name' => 'v1.2.3',
            'body' => 'changes',
            'assets' => [['browser_download_url' => 'http://dl']]
        ]);

        $service = new ReleaseService();
        $result = $service->fetchLatestRelease();
        $this->assertSame('1.2.3', $result['version']);
        $this->assertSame('changes', $result['changelog']);
        $this->assertSame('http://dl', $result['download_url']);
    }

    public function testFetchLatestReleaseReturnsNullOnFailure(): void
    {
        self::$content = false;
        $service = new ReleaseService();
        $this->assertNull($service->fetchLatestRelease());
    }
}
