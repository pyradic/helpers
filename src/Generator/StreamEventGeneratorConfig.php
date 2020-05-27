<?php

namespace Pyro\Helpers\Generator;

use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;

class StreamEventGeneratorConfig
{
    /** @var \Anomaly\Streams\Platform\Addon\Addon */
    protected $addon;

    /** @var \Anomaly\Streams\Platform\Stream\Contract\StreamInterface */
    protected $stream;

    /** @var string[]|\Illuminate\Support\Collection */
    protected $events;

    /** @var boolean */
    protected $createObserver = false;

    /** @var \Anomaly\Streams\Platform\Addon\AddonCollection */
    protected $addons;

    /** @var \Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface */
    protected $streams;

    public function __construct(AddonCollection $addons, StreamRepositoryInterface $streams)
    {
        $this->addons  = $addons;
        $this->streams = $streams;
    }

    public function getAddon()
    {
        return $this->addon;
    }

    public function setAddon($addon)
    {
        if (is_string($addon)) {
            $addon = $this->addons->get($addon);
        } elseif (is_int($addon)) {
            $addon = $this->addons->firstWhere('id', $addon);
        }
        if ($addon instanceof Addon === false) {
            throw new \InvalidArgumentException("Given addon [{$addon}] not found.");
        }
        $this->addon = $addon;
        return $this;
    }

    public function setStream($stream, $namespace = null)
    {
        if (is_string($stream)) {
            $stream = $this->streams->findBySlugAndNamespace($stream, $namespace ?? $this->addon->getSlug());
        } elseif (is_int($stream)) {
            $stream = $this->streams->find($stream);
        }
        if ($stream instanceof StreamInterface === false) {
            throw new \InvalidArgumentException("Given stream [{$stream}] not found.");
        }
        $this->stream = $stream;
        return $this;
    }

    public function getStream()
    {
        return $this->stream;
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function setEvents($events)
    {
        if (is_string($events)) {
            $events = explode(',', $events);
        }
        $this->events = collect($events);
        return $this;
    }

    public function shouldCreateObserver()
    {
        return $this->createObserver;
    }

    public function createObserver($createObserver = true)
    {
        $this->createObserver = $createObserver;
        return $this;
    }

}
