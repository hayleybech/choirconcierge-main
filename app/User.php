<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Role;

// http://alexsears.com/article/adding-roles-to-laravel-users/
// https://medium.com/@ezp127/laravel-5-4-native-user-authentication-role-authorization-3dbae4049c8a

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $notify_channels = ['database', 'mail'];
	
	/**
      * Get the roles a user has
      */
	public function roles() {
		return $this->belongsToMany('App\Role', 'users_roles');
	}
	
	 /**
      * Find out if User is an employee, based on if has any roles
      *
      * @return boolean
      */
    public function isEmployee()
    {
       $roles = $this->roles->toArray();
       return ! empty($roles);
    }
	
	 /**
      * Find out if user has a specific role
      *
      * $return boolean
      */
    public function hasRole($check)
    {
        return in_array($check, array_pluck($this->roles->toArray(), 'name'));
    }
	
	/**
     * Get key in array with corresponding value
     *
     * @return int
     */
    private function getIdInArray($array, $term)
    {
        foreach ($array as $key => $value) {
            if ($value === $term) {
                return $key;
            }
        }

        throw new UnexpectedValueException;
    }
	
	public function addRoles($ids)
    {
		$this->roles()->attach($ids);
    }
	
	public function detachRole($id) {
		
		$this->roles()->detach($id);
		
	}
	
	/**
     * Add capabilities to user
     */
	 /*
    public function addCap($title)
    {
        $assigned_roles = array();

        $roles = array_fetch(Role::all()->toArray(), 'name');
 
        switch ($title) {
            case 'admin':
                $assigned_roles[] = $this->getIdInArray($roles, 'edit_user');
                $assigned_roles[] = $this->getIdInArray($roles, 'delete_user');
            case 'music_team':
                $assigned_roles[] = $this->getIdInArray($roles, 'create_voice_placement');
                $assigned_roles[] = $this->getIdInArray($roles, 'edit_voice_placement');
            case 'membership_team':
                $assigned_roles[] = $this->getIdInArray($roles, 'create_member_profile');
                $assigned_roles[] = $this->getIdInArray($roles, 'edit_member_profile');
			case 'accounts_team':
                $assigned_roles[] = $this->getIdInArray($roles, 'add_payment');
                break;
            default:
                throw new \Exception("The role entered does not exist");
        }

        $this->roles()->attach($assigned_roles);
    }*/

}
