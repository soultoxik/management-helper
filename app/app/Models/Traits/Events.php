<?php


namespace App\Models\Traits;

trait Events
{
    public static function inserted($callback)
    {
        static::registerModelEvent('inserted', $callback);
    }

    public static function changed($callback)
    {
        static::registerModelEvent('changed', $callback);
    }

    public static function removed($callback)
    {
        static::registerModelEvent('removed', $callback);
    }

    public function getObservableEvents()
    {
        return array_merge(
            parent::getObservableEvents(),
            ['inserted', 'changed', 'removed'],
            $this->observables
        );
    }

}