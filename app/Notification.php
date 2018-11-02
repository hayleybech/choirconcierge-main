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
	
	public function getBody() {
		$replacements = array(
			'%%user.name%%' 	=> $this->user->name,
			'%%singer.name%%'	=> $this->singer->name,
		);
		
		$body_tpl = $this->notification_template->body;
		$body = str_replace( array_keys($replacements), $replacements, $body_tpl );
		
		return $body;
	}
}
