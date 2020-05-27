<?php

namespace Pyro\Helpers\Generator;

use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Entry\EntryObserver;
use File;
use Illuminate\Support\Str;
use Laradic\Generators\Core\Elements\ClassElement;

class StreamEventGenerator
{
    /** @var \Pyro\Helpers\Generator\StreamEventGeneratorConfig */
    protected $config;

    /**
     * StreamEventGenerator constructor.
     *
     * @param \Pyro\Helpers\Generator\StreamEventGeneratorConfig|array $config = ['addon' => 'my.module.clients', 'stream' => 'clients', 'events' => ['created','updated','deleted','<model-event>'], 'createObserver' => true]
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            $this->config = \App::build(StreamEventGeneratorConfig::class);
            \Hydrator::hydrate($this->config, $config);
        } elseif ($config instanceof StreamEventGeneratorConfig) {
            $this->config = $config;
        }
    }

    public function handle()
    {
        $config        = $this->config;
        $addon         = $config->getAddon();
        $addonClass    = new \ReflectionClass(get_class($addon));
        $stream        = $config->getStream();
        $baseNamespace = $addonClass->getNamespaceName();
        $baseName      = Str::studly(Str::singular($stream->getSlug()));
        $classes       = [];

        foreach ($config->getEvents() as $eventName) {
            $typeShortName = "{$baseName}Interface";
            $typeName      = "\\{$baseNamespace}\\{$baseName}\\Contract\\{$typeShortName}";
            $className     = Str::studly("{$baseName}_was_{$eventName}");
            $filePath      = $addon->getPath("src/{$baseName}/Event/{$className}.php");

            $propertyName = Str::camel($baseName);
            $class        = new ClassElement();
            $class
                ->setName($className)
                ->setNamespace("{$baseNamespace}\\{$baseName}\\Event")
                ->addUse($typeName);

            $property = $class
                ->addProperty($propertyName, 'protected')
                ->setDocBlock("@var {$typeName}");

            $getMethod = $class
                ->addMethod(Str::camel("get_{$baseName}"))
                ->setBody("        return \$this->{$propertyName};");

            $constructor = $class
                ->addMethod('__construct')
                ->addArgument($propertyName, $typeShortName)
                ->setBody("        \$this->{$propertyName} = \${$propertyName};");

            $content = $class->render();
            File::ensureDirectory(dirname($filePath));
            File::put($filePath, $content);
            $classes[] = $class;
        }

        if ($config->shouldCreateObserver()) {
            $observer = new ClassElement();
            $observer
                ->setNamespace("{$baseNamespace}\\{$baseName}")
                ->setName("{$baseName}Observer")
                ->addUse(EntryObserver::class)
                ->addUse(EntryInterface::class);
            $observer->setExtends('EntryObserver');
            foreach ($config->getEvents() as $eventName) {
                $eventClass = Str::studly("{$baseName}_was_{$eventName}");
                $observer->addUse("{$baseNamespace}\\{$baseName}\\Event\\{$eventClass}");
                $observer
                    ->addMethod($eventName)
                    ->addArgument('entry', 'EntryInterface')
                    ->setBody(<<<EOF
        parent::{$eventName}(\$entry);
        event(new {$eventClass}(\$entry));
EOF
                    );
            }
            $filePath = $addon->getPath("src/{$baseName}/{$observer->getName()}.php");
            File::put($filePath, $observer->render());
        }
    }

}
