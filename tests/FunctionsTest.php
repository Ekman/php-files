<?php

use Nekman\Files\Exceptions\FileNotFoundException;
use PHPUnit\Framework\TestCase;
use function Nekman\Files\create_directory_if_not_exists;
use function Nekman\Files\ensure_file_exists_and_readable;
use function Nekman\Files\file_read_csv;
use function Nekman\Files\file_read_lines;
use function Nekman\Files\file_write_csv;
use function Nekman\Files\file_write_lines;

class FunctionsTest extends TestCase
{
    private string $testFile;

    public function test_ensure_file_exists_and_readable(): void
    {
        ensure_file_exists_and_readable($this->testFile);
        $this->assertTrue(true);
    }

    public function test_ensure_file_exists_and_readable_file_not_exists(): void
    {
        $this->expectException(FileNotFoundException::class);
        ensure_file_exists_and_readable("./file_not_exists");
    }

    public function test_file_read_lines(): void
    {
        $this->assertEquals(
            ["foo,bar", "hello,world"],
            $this->iterable_to_array(file_read_lines($this->testFile))
        );
    }

    private function iterable_to_array(iterable $it): array
    {
        $array = [];
        foreach ($it as $value) {
            $array[] = $value;
        }
        return $array;
    }

    public function test_read_csv(): void
    {
        $this->assertEquals(
            [["foo", "bar"], ["hello", "world"]],
            $this->iterable_to_array(file_read_csv($this->testFile))
        );
    }

    public function test_create_directory_if_not_exists(): void
    {
        $dir = sys_get_temp_dir() . uniqid();
        $this->assertFileDoesNotExist($dir);

        create_directory_if_not_exists($dir);
        $this->assertFileExists($dir);

        // Test calling the directory again, everything should remain the same
        create_directory_if_not_exists($dir);
        $this->assertFileExists($dir);
    }

    public function test_file_write_lines(): void
    {
        $expected = ["x", "y", "z"];

        $file = sys_get_temp_dir() . uniqid();
        $this->assertFileDoesNotExist($file);

        file_write_lines($file, $expected);
        $this->assertFileExists($file);

        $this->assertEquals(
            $expected,
            $this->iterable_to_array(file_read_lines($file))
        );
    }

    public function test_file_write_csv(): void
    {
        $expected = [["x", "y"], ["z", "a"]];

        $file = sys_get_temp_dir() . uniqid();
        $this->assertFileDoesNotExist($file);

        file_write_csv($file, $expected);
        $this->assertFileExists($file);

        $this->assertEquals(
            $expected,
            $this->iterable_to_array(file_read_csv($file))
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Set up a test state
        file_put_contents(
            $this->testFile = sys_get_temp_dir() . "/nekman_files.csv",
            "foo,bar" . PHP_EOL . "hello,world" . PHP_EOL,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        //unlink($this->testFile);
    }
}
