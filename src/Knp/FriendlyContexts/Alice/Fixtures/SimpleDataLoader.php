<?php

namespace Knp\FriendlyContexts\Alice\Fixtures;

use Nelmio\Alice\DataLoaderInterface;
use Nelmio\Alice\FixtureBuilderInterface;
use Nelmio\Alice\GeneratorInterface;
use Nelmio\Alice\IsAServiceTrait;
use Nelmio\Alice\ObjectSet;

class SimpleDataLoader implements DataLoaderInterface
{
    use IsAServiceTrait;

    /**
     * @var FixtureBuilderInterface
     */
    private $builder;

    /**
     * @var GeneratorInterface
     */
    private $generator;

    public function __construct(FixtureBuilderInterface $fixtureBuilder, GeneratorInterface $generator)
    {
        $this->builder = $fixtureBuilder;
        $this->generator = $generator;
    }

    public $fixtureSet;

    /**
     * @inheritdoc
     */
    public function loadData(array $data, array $parameters = [], array $objects = []) : ObjectSet
    {
        $fixtureSet = $this->builder->build($data, $parameters, $objects);
        $this->fixtureSet = $fixtureSet;

        return $this->generator->generate($fixtureSet);
    }
}
