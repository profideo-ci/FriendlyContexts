<?php

namespace Knp\FriendlyContexts\Alice\Fixtures;

use Knp\FriendlyContexts\Alice\ProviderResolver;
use Nelmio\Alice\Loader\NativeLoader as BaseLoader;

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


class Loader extends BaseLoader
{
    private $fixtureSet;
    private $fixtureData;
    private $loader;
    private $dataLoader;

    public function __construct($locale, ProviderResolver $providers)
    {
        parent::__construct();
        $this->loader = $this->createFilesLoader();
        $this->dataLoader = $this->getDataLoader();
    }

    public function getCache()
    {
        $cache = [];
        foreach ($this->fixtureSet->getFixtures() as $fixture) {
            $spec = [];
            foreach ($fixture->getSpecs()->getProperties()->getIterator() as $property) {
                $spec[] = $property->getValue();
            }
            $cache[] = [$spec, $this->fixtureData[$fixture->getId()]];
        }

        return $cache;
    }

    public function clearCache()
    {
        $this->fixtureSet = null;
    }

    public function load($filename)
    {
        $this->fixtureData = $this->loader->loadFiles($filename)->getObjects();
        $this->fixtureSet = $this->dataLoader->fixtureSet;

        return $this->fixtureData;
    }

    protected function createDataLoader() : DataLoaderInterface
    {
        return new SimpleDataLoader(
            $this->getFixtureBuilder(),
            $this->getGenerator()
        );
    }
}
