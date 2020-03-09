<?php declare(strict_types=1);

namespace Parable\GetSet\Tests;

use Parable\GetSet\CookieCollection;
use Parable\GetSet\FilesCollection;
use Parable\GetSet\GetCollection;
use Parable\GetSet\DataCollection;
use Parable\GetSet\PostCollection;
use Parable\GetSet\ServerCollection;
use Parable\GetSet\SessionCollection;
use PHPUnit\Framework\TestCase;

class SimpleCollectionsTest extends TestCase
{
    public function testCookieCollection(): void
    {
        $_COOKIE['hello'] = 'yay';

        $cookie = new CookieCollection();

        $cookie->set('test', 'hello');

        self::assertSame(
            [
                'hello' => 'yay',
                'test' => 'hello',
            ],
            $cookie->getAll()
        );
    }

    public function testFilesCollection(): void
    {
        $files = new FilesCollection();

        $files->set('test', 'hello');

        self::assertSame(
            [
                'test' => 'hello',
            ],
            $files->getAll()
        );
    }

    public function testGetCollection(): void
    {
        $get = new GetCollection();

        $get->set('test', 'hello');

        self::assertSame(
            [
                'test' => 'hello',
            ],
            $get->getAll()
        );
    }

    public function testInternalCollection(): void
    {
        $internal = new DataCollection();

        $internal->set('test', 'hello');

        self::assertSame(
            [
                'test' => 'hello',
            ],
            $internal->getAll()
        );
    }

    public function testPostCollection(): void
    {
        $post = new PostCollection();

        $post->set('test', 'hello');

        self::assertSame(
            [
                'test' => 'hello',
            ],
            $post->getAll()
        );
    }

    public function testServerCollection(): void
    {
        $server = new ServerCollection();

        $count = $server->count();

        $server->set('test', 'hello');

        self::assertCount($count + 1, $server->getAll());
        self::assertArrayHasKey('test', $server->getAll());
    }

    public function testSessionCollection(): void
    {
        $session = new SessionCollection();

        $session->set('test', 'hello');

        self::assertSame(
            [
                'test' => 'hello',
            ],
            $session->getAll()
        );
    }
}
