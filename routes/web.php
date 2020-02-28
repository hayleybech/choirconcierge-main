<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::get('/', function () {
	return view('welcome');
})->name('menu');
	

// Basic dashboard auth
Route::middleware('auth')->group(function() {

	Route::get('/dash', 'DashController@index')->name('dash');
	
});


// Basic employee auth
Route::middleware(['auth', 'employee'])->group(function() {

	Route::get('/singers', 'SingersController@index')->name('singers.index');
	
	Route::get('/singers/create', 'SingersController@create')->name('singer.create');
	Route::post('/singers', 'SingersController@store');
	
	Route::get('/singers/export', 'SingersController@export')->name('singers.export');

	Route::get('/singers/{singer}', 'SingersController@show')->name('singers.show');
	Route::get('/singers/{singer}/edit', 'SingersController@edit')->name('singers.edit');
	Route::put('/singers/{singer}', 'SingersController@update')->name('singers.update');

	Route::get('/singers/{singer}/tasks/{task}/complete', 'SingersController@completeTask')->name('task.complete');

    Route::get('/singers/{singer}/delete', 'SingersController@delete')->name('singer.delete');

	Route::resource('/notifications', 'NotificationController');

    Route::get('/songs', 'SongsController@index')->name('songs.index');
    Route::get('/songs/create', 'SongsController@create')->name('song.create');
    Route::get('/songs/learning', 'SongsController@learning')->name('songs.learning');
    Route::post('/songs', 'SongsController@store');
    Route::get('/songs/{song}', 'SongsController@show')->name('songs.show');
    Route::get('/songs/{song}/edit', 'SongsController@edit')->name('song.edit');
    Route::put('/songs/{song}', 'SongsController@update');
    Route::get('/songs/{song}/delete', 'SongsController@delete')->name('song.delete');

    Route::post('/songs/{song}/attachments', 'SongAttachmentController@store')->name('song.attachments.store');
    Route::get('/songs/{song}/attachments/{attachment}/delete', 'SongAttachmentController@delete')->name('song.attachments.delete');

});

// Membership Team auth
Route::middleware(['auth', 'role:Membership Team'])->group(function() {
	
	// Old (drip) version
	Route::get('/singers/{singer}/memberprofile', function($email){
		return redirect()->away( config('app.member_profile_edit') . urlencode($email) );
	})->name('singer.memberprofile');
	
	Route::get('/memberprofile', function(){
		return redirect()->away( config('app.member_profile_new') );
	})->name('memberprofile.new');
	
	
	// New version
	Route::get('singers/{singer}/profile/create', 'SingersController@createProfile')->name('profile.create');
	Route::post('singers/{singer}/profile', 'SingersController@storeProfile')->name('profile');
	
	Route::get('/singers/{singer}/account/created', 'SingersController@markAccountCreated')->name('singer.account.created');

	Route::get('/singers/{singer}/move/', 'SingersController@move')->name('singer.move');

	
});

// Music Team auth
Route::middleware(['auth', 'role:Music Team'])->group(function() {

    // Old (drip) version
	Route::get('/singers/{singer}/voiceplacement', function($email){
		return redirect()->away( config('app.voice_placement_edit') . urlencode($email) );
	})->name('singer.voiceplacement');

	Route::get('/voiceplacement', function(){
		return redirect()->away( config('app.voice_placement_new') );
	})->name('voiceplacement.new');

	// New version
    Route::get('singers/{singer}/placement/create', 'SingersController@createPlacement')->name('placement.create');
    Route::post('singers/{singer}/placement', 'SingersController@storePlacement')->name('placement');

	Route::get('/singers/{singer}/audition/pass', 'SingersController@auditionpass')->name('singer.audition.pass');
	
});

// Accounts Team auth
Route::middleware(['auth', 'role:Accounts Team'])->group(function() {
	
	Route::get('/singers/{singer}/fees/paid', 'SingersController@feespaid')->name('singer.fees.paid');
	
});

// Uniforms Team auth
Route::middleware(['auth', 'role:Uniforms Team'])->group(function() {
	
	Route::get('/singers/{singer}/uniform/provided', 'SingersController@markUniformProvided')->name('singer.uniform.provided');
	
});


// Admin level auth
Route::middleware(['auth', 'role:Admin'])->group(function() {
	
	Route::get('/users', 'UsersController@index')->name('users.index');

	Route::get('/users/{user}/roles/{role}/detach', 'UsersController@detachRole')->name('users.detachrole');

	Route::post('/users/{user}/role', 'UsersController@addRoles')->name('users.addroles');	
	
	Route::get('/tasks', 'TasksController@index')->name('tasks.index');
	
	Route::resource('/notification-templates', 'NotificationTemplateController');
	
	// Super admin?
	
	Route::get('/migrate', function(){
		echo Artisan::call('migrate', [
		    '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
	});
    Route::get('/migrate/status', function(){
        echo Artisan::call('migrate:status');
        echo '<pre>'.Artisan::output().'</pre>';
    });

        // DO NOT RUN ON PRODUCTION
        Route::get('/seed/users', function(){
            echo Artisan::call('db:seed', ['--class' => 'UserTableSeeder']);
            echo '<pre>'.Artisan::output().'</pre>';
        });

        Route::get('/seed/singers', function(){
            echo Artisan::call('db:seed', [
                '--class' => 'SingerTableSeeder',
                '--force' => true,
            ]);
            echo '<pre>'.Artisan::output().'</pre>';
        });

        // ONLY RUN ONCE
        Route::get('/seed/singer-categories', function(){
            echo Artisan::call('db:seed', [
                '--class' => 'SingerCategorySeeder',
                '--force' => true,
            ]);
            echo '<pre>'.Artisan::output().'</pre>';
        });

        Route::get('/seed/tasks', function(){
            echo Artisan::call('db:seed', [
                '--class' => 'TaskTableSeeder',
                '--force' => true,
            ]);
            echo '<pre>'.Artisan::output().'</pre>';
        });


        Route::get('/seed/templates', function(){
            echo Artisan::call('db:seed', [
                '--class' => 'NotificationTemplateSeeder',
                '--force' => true,
            ]);
            echo '<pre>'.Artisan::output().'</pre>';
        });


    Route::get('/migrate/refresh', function(){
		echo Artisan::call('migrate:refresh');
        echo '<pre>'.Artisan::output().'</pre>';
	});

	Route::get('/migrate/refresh/seed', function(){
		echo Artisan::call('migrate:refresh', [
			'--seed' => true,
		]);
        echo '<pre>'.Artisan::output().'</pre>';
	});

	Route::get('/migrate/rollback', function(){
		echo Artisan::call('migrate:rollback');
        echo '<pre>'.Artisan::output().'</pre>';
	});

	Route::get('/migrate/reset', function(){
		echo Artisan::call('migrate:reset');
        echo '<pre>'.Artisan::output().'</pre>';
	});

	Route::get('/migrate/fresh', function(){
		echo Artisan::call('migrate:fresh');
        echo '<pre>'.Artisan::output().'</pre>';
	});

	Route::get('/migrate/fresh/seed', function(){
		echo Artisan::call('migrate:fresh', [
			'--seed' => true,
		]);
        echo '<pre>'.Artisan::output().'</pre>';
	});

	Route::get('/import', 'SingersController@import');
	
});
