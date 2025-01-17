<?php

namespace Spatie\TypeScriptTransformer\Tests\TypeReflectors;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\TypeReflectors\MethodParameterTypeReflector;

class MethodParameterTypeReflectorTest extends TestCase
{
    /** @test */
    public function it_can_reflect_from_reflection()
    {
        $class = new class {
            public function method(
                int $int,
                ?int $nullable_int,
                int | float $union,
                null | int | float $nullable_union,
                $without_type
            ) {
            }
        };

        $parameters = (new ReflectionMethod($class, 'method'))->getParameters();

        $this->assertEquals(
            'int',
            (string) MethodParameterTypeReflector::create($parameters[0])->reflect()
        );

        $this->assertEquals(
            '?int',
            (string) MethodParameterTypeReflector::create($parameters[1])->reflect()
        );

        $this->assertEquals(
            'int|float',
            (string) MethodParameterTypeReflector::create($parameters[2])->reflect()
        );

        $this->assertEquals(
            'int|float|null',
            (string) MethodParameterTypeReflector::create($parameters[3])->reflect()
        );

        $this->assertEquals(
            'any',
            (string) MethodParameterTypeReflector::create($parameters[4])->reflect()
        );
    }

    /** @test */
    public function it_can_reflect_from_docblock()
    {
        $class = new class {
            /**
             * @param int $int
             * @param ?int $nullable_int
             * @param int|float $union
             * @param int|float|null $nullable_union
             * @param array<array-key, string> $array
             * @param $without_type
             */
            public function method(
                $int,
                $nullable_int,
                $union,
                $nullable_union,
                $array,
                $without_type
            ) {
            }
        };

        $parameters = (new ReflectionMethod($class, 'method'))->getParameters();

        $this->assertEquals(
            'int',
            (string) MethodParameterTypeReflector::create($parameters[0])->reflect()
        );

        $this->assertEquals(
            '?int',
            (string) MethodParameterTypeReflector::create($parameters[1])->reflect()
        );

        $this->assertEquals(
            'int|float',
            (string) MethodParameterTypeReflector::create($parameters[2])->reflect()
        );

        $this->assertEquals(
            'int|float|null',
            (string) MethodParameterTypeReflector::create($parameters[3])->reflect()
        );

        $this->assertEquals(
            'array<array-key,string>',
            (string) MethodParameterTypeReflector::create($parameters[4])->reflect()
        );

        $this->assertEquals(
            'any',
            (string) MethodParameterTypeReflector::create($parameters[5])->reflect()
        );
    }

    /** @test */
    public function it_cannot_reflect_from_attribute()
    {
        $class = new class {
            #[LiteralTypeScriptType('int')]
            public function method(
                $int,
            ) {
            }
        };

        $parameters = (new ReflectionMethod($class, 'method'))->getParameters();

        $this->assertEquals(
            'any',
            (string) MethodParameterTypeReflector::create($parameters[0])->reflect()
        );
    }
}
