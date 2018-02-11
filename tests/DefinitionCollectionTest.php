<?php

declare(strict_types=1);

namespace FppTest;

use Fpp\Argument;
use Fpp\Definition;
use Fpp\DefinitionCollection;
use Fpp\DefinitionCollectionDumper;
use Fpp\Deriving\Equals;
use Fpp\Dumper\Dumper;
use Fpp\Type\Data;
use PHPUnit\Framework\TestCase;

class DefinitionCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_adds_definitions(): void
    {
        $arguments = [
            new Argument('', 'name', 'string', false),
            new Argument('', 'age', 'int', false),
        ];
        $derivings = [new Equals()];
        $definition = new Definition(new Data(), 'Foo\Bar', 'Person', $arguments, $derivings);

        $collection = new DefinitionCollection();
        $collection->addDefinition($definition);

        $this->assertCount(1, $collection->definitions());

        $this->assertTrue($collection->hasDefinition('Foo\Bar', 'Person'));
        $this->assertSame($definition, $collection->getDefinition('Foo\Bar', 'Person'));
        $this->assertNull($collection->getDefinition('Foo\Bar', 'Unknown'));
    }

    /**
     * @test
     */
    public function it_forbids_duplicate_definitions(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $arguments = [
            new Argument('', 'name', 'string', false),
            new Argument('', 'age', 'int', false),
        ];
        $derivings = [new Equals()];
        $definition = new Definition(new Data(), 'Foo\Bar', 'Person', $arguments, $derivings);

        $collection = new DefinitionCollection();
        $collection->addDefinition($definition);
        $collection->addDefinition($definition);
    }

    /**
     * @test
     */
    public function it_merges_definitions(): void
    {
        $arguments = [
            new Argument('', 'name', 'string', false),
            new Argument('', 'age', 'int', false),
        ];
        $derivings = [new Equals()];
        $definition = new Definition(new Data(), 'Foo\Bar', 'Person', $arguments, $derivings);

        $collection = new DefinitionCollection();
        $collection->addDefinition($definition);

        $arguments = [
            new Argument('', 'name', 'string', false),
            new Argument('', 'age', 'int', false),
        ];
        $derivings = [new Equals()];
        $definition = new Definition(new Data(), 'Foo\Baz', 'Person', $arguments, $derivings);

        $collection2 = new DefinitionCollection();
        $collection2->addDefinition($definition);

        $collection3 = $collection->merge($collection2);

        $this->assertNotSame($collection, $collection2);
        $this->assertNotSame($collection2, $collection3);
        $this->assertNotSame($collection, $collection3);

        $this->assertCount(2, $collection3->definitions());
    }

    /**
     * @test
     */
    public function it_forbids_duplicate_definitions_during_merge(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $arguments = [
            new Argument('', 'name', 'string', false),
            new Argument('', 'age', 'int', false),
        ];
        $derivings = [new Equals()];
        $definition = new Definition(new Data(), 'Foo\Bar', 'Person', $arguments, $derivings);

        $collection = new DefinitionCollection();
        $collection->addDefinition($definition);

        $collection2 = new DefinitionCollection();
        $collection2->addDefinition($definition);

        $collection->merge($collection2);
    }
}
