<?php

namespace Pyro\Helpers\Entry;

/**
 * @mixin \Anomaly\Streams\Platform\Entry\EntryObserver
 */
trait HasObserverDefaults
{

    protected function addDefault($model, $key, $defaultValue)
    {
        if ( ! isset($model->{$key})) {
            $model->{$key} = $defaultValue;
        }
        return $this;
    }

}
