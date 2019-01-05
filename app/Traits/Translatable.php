<?php

namespace App\Traits;

use App\Translation;
use Illuminate\Support\Facades\App;

trait Translatable
{
    /**
     * Get all of the models's translations.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Get the translation attribute.
     *
     * @return \App\Translation
     */
    public function getTranslationAttribute()
    {
        return $this->translations->where('language', App::getLocale())->first();
    }

    /**
     * Get the translation.
     *
     * @return string
     */
    public function transResponse($instance, $key)
    {
        // App::setLocale('fr');

        $locale = App::getLocale();

        $attributes = ['translatable_id'=> $instance->id, 'language'=> $locale];

        if($instance->translations()->where($attributes)->exists()) {
            if(isset($instance->translation->content[$key])) {
                return $instance->translation->content[$key];
            }
        }

        return $instance->$key;
    }
}