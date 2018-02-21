<?php

declare(strict_types=1);

namespace FppTest;

use Fpp\Constructor;
use Fpp\Definition;
use Fpp\DefinitionCollection;
use Fpp\Deriving\FromString;
use Fpp\Deriving\ToString;
use PHPUnit\Framework\TestCase;
use const Fpp\loadTemplate;
use const Fpp\replace;
use function Fpp\dump;

class DumpTest extends TestCase
{
    /**
     * @var callable
     */
    private $dump;

    protected function setUp(): void
    {
        $this->dump = function (DefinitionCollection $collection): string {
            return dump($collection, loadTemplate, replace);
        };
    }

    /**
     * @test
     * @group by
     */
    public function it_dumps_simple_class(): void
    {
        $dump = $this->dump;

        $definition = new Definition('Foo', 'Bar', [new Constructor('String')]);
        $collection = $this->buildCollection($definition);

        $expected = <<<CODE
<?php
// this file is auto-generated by prolic/fpp
// don't edit this file manually

declare(strict_types=1);

namespace Foo {
    final class Bar
    {
        private \$value;

        public function __construct(string \$value)
        {
            \$this->value = \$value;
        }

        public function value(): string
        {
            return \$this->value;
        }
    }
}

CODE;

        $this->assertSame($expected, $dump($collection));
    }

    /**
     * @test
     */
    public function it_dumps_class_incl_its_child(): void
    {
        $dump = $this->dump;

        $definition = new Definition(
            'Foo',
            'Bar',
            [
                new Constructor('Foo\Bar'),
                new Constructor('Foo\Baz'),
            ]
        );

        $collection = $this->buildCollection($definition);

        $expected = <<<CODE
<?php
// this file is auto-generated by prolic/fpp
// don't edit this file manually

declare(strict_types=1);

namespace Foo {
    class Bar
    {
    }
}

namespace Foo {
    final class Baz extends Bar
    {
    }
}

CODE;

        $this->assertSame($expected, $dump($collection));
    }

    private function buildCollection(Definition ...$definition): DefinitionCollection
    {
        $collection = new DefinitionCollection();

        foreach (func_get_args() as $arg) {
            $collection->addDefinition($arg);
        }

        return $collection;
    }
}
