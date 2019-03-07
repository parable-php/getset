<?php declare(strict_types=1);

namespace Parable\GetSet\Tests;

use Parable\GetSet\BaseCollection;
use Parable\GetSet\ServerCollection;
use Parable\GetSet\Exception;
use Parable\GetSet\Resource\GlobalResourceInterface;
use Parable\GetSet\Resource\LocalResourceInterface;

class GetSetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BaseCollection
     */
    protected $getSet;

    public function setUp()
    {
        parent::setUp();

        $this->getSet = new class extends BaseCollection implements LocalResourceInterface {
        };
    }

    public function testLocalResourceGetSet()
    {
        $globalsBefore = $GLOBALS;

        $this->getSet->set('test', 'value');

        self::assertSame(
            ['test' => 'value'],
            $this->getSet->getAll()
        );

        self::assertSame(
            $globalsBefore,
            $GLOBALS
        );
    }

    public function testGlobalResourceGetSet()
    {
        $getSet = new class extends BaseCollection implements GlobalResourceInterface {
            public function getResource(): string
            {
                return '_TEST';
            }
        };

        $getSet->set('test', 'value');

        self::assertSame(
            ['test' => 'value'],
            $getSet->getAll()
        );

        self::assertArrayHasKey('_TEST', $GLOBALS);
        self::assertArrayHasKey('test', $GLOBALS['_TEST']);
        self::assertSame('value', $GLOBALS['_TEST']['test']);
    }

    public function testNoResourceInterfaceSetThrowsExceptionOnGetAll()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("No resource interface implemented.");

        $getSet = new class extends BaseCollection {
        };

        $getSet->getAll();
    }

    public function testNoResourceInterfaceSetThrowsExceptionOnSetAll()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("No resource interface implemented.");

        $getSet = new class extends BaseCollection {
        };

        $getSet->setAll([]);
    }

    public function testSetAllGetAll()
    {
        $this->getSet->setAll([
            'key1' => 'yo1',
            'key2' => 'yo2',
        ]);

        self::assertSame(
            [
                'key1' => 'yo1',
                'key2' => 'yo2',
            ],
            $this->getSet->getAll()
        );
    }

    public function testGetAllAndClear()
    {
        $this->getSet->setAll([
            'key1' => 'yo1',
            'key2' => 'yo2',
        ]);

        self::assertSame(
            [
                'key1' => 'yo1',
                'key2' => 'yo2',
            ],
            $this->getSet->getAllAndClear()
        );

        self::assertSame([], $this->getSet->getAll());
    }

    public function testGetAndRemove()
    {
        $this->getSet->setAll([
            'key1' => 'yo1',
            'key2' => 'yo2',
        ]);

        self::assertSame(
            'yo1',
            $this->getSet->getAndRemove('key1')
        );

        self::assertSame(
            [
                'key2' => 'yo2',
            ],
            $this->getSet->getAll()
        );
        self::assertSame(1, $this->getSet->count());
    }

    public function testRemoveNonExistingKeyThrows()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Cannot remove non-existing value by key 'stuff'");

        $this->getSet->setMany([
            'key1' => 'yo1',
            'key2' => 'yo2',
            'key3' => 'yo3',
        ]);

        self::assertSame(3, $this->getSet->count());

        $this->getSet->remove('stuff');

        self::assertSame(3, $this->getSet->count());
    }

    public function testSetGetSpecificAndGetAll()
    {
        $this->getSet->set('key1', 'yo1');
        $this->getSet->set('key2', 'yo2');
        $this->getSet->set('key3', 'yo3');

        self::assertSame('yo3', $this->getSet->get('key3'));

        self::assertSame(
            [
                'key1' => 'yo1',
                'key2' => 'yo2',
                'key3' => 'yo3',
            ],
            $this->getSet->getAll()
        );
    }

    public function testSetAllVersusSetMany()
    {
        self::assertCount(0, $this->getSet->getAll());

        $this->getSet->setAll([
            'temp1' => 'yo1',
            'temp2' => 'yo2',
            'temp3' => 'yo3',
        ]);

        self::assertCount(3, $this->getSet->getAll());

        self::assertSame(
            [
                'temp1' => 'yo1',
                'temp2' => 'yo2',
                'temp3' => 'yo3',
            ],
            $this->getSet->getAll()
        );

        // setAll() overwrites all values, discarding the pre-existing ones
        $this->getSet->setAll([
            'key1' => 'yo1',
            'key2' => 'yo2',
        ]);

        self::assertSame(
            [
                'key1' => 'yo1',
                'key2' => 'yo2',
            ],
            $this->getSet->getAll()
        );

        // setMany() overwrites all values passed if they exist, but leaves pre-existing ones intact
        $this->getSet->setMany([
            'key1' => 'this is new',
            'key3' => 'this is new as well',
        ]);

        self::assertSame(
            [
                'key1' => 'this is new',
                'key2' => 'yo2',
                'key3' => 'this is new as well',
            ],
            $this->getSet->getAll()
        );
    }

    public function testGetSetAndRemoveWithHierarchalKeys()
    {
        $this->getSet->set('one', ['this' => 'should stay']);
        $this->getSet->set('one.two.three.four', 'totally nested, yo');

        self::assertSame(
            [
                'this' => 'should stay',
                'two' => [
                    'three' => [
                        'four' => 'totally nested, yo',
                    ],
                ],
            ],
            $this->getSet->get('one')
        );

        self::assertSame(
            [
                'one' => [
                    'this' => 'should stay',
                    'two' => [
                        'three' => [
                            'four' => 'totally nested, yo',
                        ],
                    ],
                ],
            ],
            $this->getSet->getAll()
        );

        $this->getSet->remove('one.this');

        self::assertSame(
            [
                'one' => [
                    'two' => [
                        'three' => [
                            'four' => 'totally nested, yo',
                        ],
                    ],
                ],
            ],
            $this->getSet->getAll()
        );

        self::assertSame(
            [
                'four' => 'totally nested, yo',
            ],
            $this->getSet->getAndRemove('one.two.three')
        );

        // And since 'three' is now removed, 'two' will be empty.
        self::assertSame(
            [
                'one' => [
                    'two' => [
                    ],
                ],
            ],
            $this->getSet->getAll()
        );
    }

    public function testRemoveHierarchalKey()
    {
        $this->getSet->set('one.two.three', 'totally');
        $this->getSet->set('one.two.four', 'also');

        self::assertCount(2, $this->getSet->get('one.two'));
        self::assertSame('totally', $this->getSet->get('one.two.three'));

        $this->getSet->remove('one.two.three');

        self::assertSame(null, $this->getSet->get('one.two.three'));

        self::assertCount(1, $this->getSet->get('one.two'));
        self::assertTrue(is_array($this->getSet->get('one.two')));

        $this->getSet->remove('one.two');

        self::assertNull($this->getSet->get('one.two'));

        // But one should be untouched and still an array
        self::assertTrue(is_array($this->getSet->get('one')));
    }

    public function testCountWithKey()
    {
        $this->getSet->set('one.two.three', 'totally');
        $this->getSet->set('one.two.four', 'also');

        // count only counts the top level, which is 1: ['one']
        self::assertSame(1, $this->getSet->count());
        // this level is 1: ['two']
        self::assertSame(1, $this->getSet->count('one'));
        // but 'two' contains both 'three' and 'four'
        self::assertSame(2, $this->getSet->count('one.two'));
    }

    public function testHas()
    {
        $this->getSet->set('one.two.three', 'totally');

        self::assertTrue($this->getSet->has('one'));
        self::assertTrue($this->getSet->has('one.two'));
        self::assertTrue($this->getSet->has('one.two.three'));

        self::assertFalse($this->getSet->has('one.two.three.totally'));
        self::assertFalse($this->getSet->has('random'));
    }

    public function testGetNonExistingKeyReturnsNullByDefault()
    {
        self::assertNull($this->getSet->get('nope'));
    }

    public function testGetNonExistingKeyReturnsDefaultIfPassed()
    {
        self::assertSame('default', $this->getSet->get('nope', 'default'));
    }

    public function testGlobalValuesAreSetGlobally()
    {
        $server = new ServerCollection();

        $server->set('testing.this', 'works');

        self::assertSame('works', $_SERVER['testing']['this']);
    }
}
