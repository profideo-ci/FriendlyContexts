<?php

namespace Knp\FriendlyContexts\Alice\Fixtures;

use Knp\FriendlyContexts\Alice\ProviderResolver;
use Nelmio\Alice\Loader\NativeLoader as BaseLoader;
use Nelmio\Alice\DataLoaderInterface;
use Nelmio\Alice\FixtureBuilderInterface;
use Nelmio\Alice\GeneratorInterface;
use Nelmio\Alice\IsAServiceTrait;
use Nelmio\Alice\ObjectSet;

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
        if ($this->fixtureSet) {
            foreach ($this->fixtureSet->getFixtures() as $fixture) {
                $spec = [];
                foreach ($fixture->getSpecs()->getProperties()->getIterator() as $property) {
                    $spec[] = $property->getValue();
                }
                $cache[] = [$spec, $this->fixtureData[$fixture->getId()]];
            }
        }

        return $cache;
    }

    public function clearCache()
    {
        $this->fixtureSet = null;
    }

    public function load($filename)
    {
        $this->fixtureData = $this->loader->loadFiles([$filename])->getObjects();
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
