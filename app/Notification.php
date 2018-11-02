<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function notification_template(){
        return $this->belongsTo('App\NotificationTemplate', 'template_id');
    }

    public function singer() {
        return $this->belongsTo('App\Singer');
    }
}
