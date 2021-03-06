<?php

/**
 * This file is part of prolic/fpp.
 * (c) 2018-2019 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FppTest;

use Fpp\Constructor;
use Fpp\Definition;
use Fpp\DefinitionCollection;
use function Fpp\replace;
use PHPUnit\Framework\TestCase;

class ReplaceTest extends TestCase
{
    /**
     * @test
     */
    public function it_replaces_placeholders_and_cleans_up_formatting(): void
    {
        $builders = [
            'one' => function () {
                return 'private $one;';
            },
            'two' => function () {
                return 'private $two;';
            },
        ];

        $template = <<<TEMPLATE
namespace Test;

class Foo
{
    {{one}}
    {{one}}
    {{two}}

    public function baz(): void
    {
    }
    public function bar(): void
    {
    }
    
}

TEMPLATE;

        $expected = <<<TEMPLATE
namespace Test;

class Foo
{
    private \$one;
    private \$one;
    private \$two;

    public function baz(): void
    {
    }

    public function bar(): void
    {
    }
}


TEMPLATE;

        /** @var Definition */
        $definition = $this->prophesize(Definition::class)->reveal();

        /** @var Constructor */
        $constructor = $this->prophesize(Constructor::class)->reveal();

        /** @var DefinitionCollection */
        $collection = $this->prophesize(DefinitionCollection::class)->reveal();

        $this->assertSame($expected, replace($template, $definition, $constructor, $collection, $builders));
    }
}
