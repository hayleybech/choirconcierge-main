<?php

namespace App\Models\Traits;

trait OnDeleteRestrict
{
	protected static function bootOnDeleteRestrict(): void
	{
		static::deleting(static function ($model) {
			foreach (static::$delete_restricted as $relationship) {
				if ($model->$relationship()->count() > 0) {
					return false;
				}
			}
			return true;
		});
	}
}
