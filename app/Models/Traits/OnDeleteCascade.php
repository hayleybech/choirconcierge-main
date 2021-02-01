<?php


namespace App\Models\Traits;


trait OnDeleteCascade
{
    protected static function bootOnDeleteCascade(): void
    {
        static::deleting(static function($model){
            foreach(static::$delete_cascaded as $relationship)
            {
                if( $model->$relationship()->count() > 0) {
                    $model->$relationship()->get()->each(static function($child){
                        $child->delete();
                    });
                }
            }
        });
    }
}