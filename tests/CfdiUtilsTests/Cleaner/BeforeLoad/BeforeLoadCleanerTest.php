<?php

namespace CfdiUtilsTests\Cleaner\BeforeLoad;

use CfdiUtils\Cleaner\BeforeLoad\BeforeLoadCleaner;
use CfdiUtils\Cleaner\BeforeLoad\BeforeLoadCleanerInterface;
use CfdiUtilsTests\TestCase;

final class BeforeLoadCleanerTest extends TestCase
{
    public function testImplementsBeforeLoadCleanerInterface(): void
    {
        $this->assertInstanceOf(BeforeLoadCleanerInterface::class, new BeforeLoadCleaner());
    }

    public function testDefaultCleaners(): void
    {
        $cleaner = new BeforeLoadCleaner();
        $this->assertEquals($cleaner->members(), BeforeLoadCleaner::defaultCleaners());
        $this->assertCount(2, $cleaner->members());
    }

    public function testCleanCallsCleaners(): void
    {
        $returnFoo = new class () implements BeforeLoadCleanerInterface {
            public function clean(string $content): string
            {
                return str_replace('foo', 'FOO', $content);
            }
        };
        $returnBar = new class () implements BeforeLoadCleanerInterface {
            public function clean(string $content): string
            {
                return str_replace('bar', 'BAR', $content);
            }
        };
        $cleaner = new BeforeLoadCleaner($returnFoo, $returnBar);
        $transformed = $cleaner->clean('foo bar baz');
        $expected = 'FOO BAR baz';
        $this->assertSame($expected, $transformed);
    }
}
