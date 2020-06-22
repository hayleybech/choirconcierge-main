<?php

/**
 * WELCOME
 */
// Home
Breadcrumbs::for('menu', static function($trail){
    $trail->push('Home', route('menu'));
});

/**
 * AUTH
 */
// Home > Login
Breadcrumbs::for('login', static function ($trail) {
    $trail->parent('menu');
    $trail->push('Login', route('login'));
});

// Home > Password > Forgot
Breadcrumbs::for('password.request', static function ($trail) {
    $trail->parent('menu');
    $trail->push('Password');
    $trail->push('Forgot', route('password.request'));
});
// Home > Password > Reset
Breadcrumbs::for('password.reset', static function ($trail, $token) {
    $trail->parent('menu');
    $trail->push('Password');
    $trail->push('Reset', route('password.reset', $token));
});

/**
 * DASHBOARD
 */
Breadcrumbs::for('dash', static function($trail){
    $trail->push('Dashboard', route('dash'));
});

/**
 * SINGERS
 */

// Singers
Breadcrumbs::for('singers.index', static function ($trail) {
    $trail->parent('dash');
    $trail->push('Singers', route('singers.index'));
});

// Singers > [Singer]
Breadcrumbs::for('singers.show', static function ($trail, $singer) {
    $trail->parent('singers.index');
    $trail->push($singer->name, route('singers.show', $singer));
});

// Singers > [Singer] > Edit
Breadcrumbs::for('singers.edit', static function ($trail, $singer) {
    $trail->parent('singers.show', $singer);
    $trail->push('Edit', route('singers.edit', $singer));
});

// Singers > [Singer] > Create Member Profile
Breadcrumbs::for('profile.create', static function ($trail, $singer) {
    $trail->parent('singers.show', $singer);
    $trail->push('Create Member Profile', route('profile.create', $singer));
});

// Singers > [Singer] > [Member Profile] > Edit
Breadcrumbs::for('profiles.edit', static function ($trail, $singer, $profile) {
    $trail->parent('singers.show', $singer);
    $trail->push('Edit Member Profile', route('profiles.edit', [$singer, $profile]));
});


// Singers > [Singer] > Create Voice Placement
Breadcrumbs::for('placement.create', static function ($trail, $singer) {
    $trail->parent('singers.show', $singer);
    $trail->push('Create Voice Placement', route('placement.create', $singer));
});
// Singers > [Singer] > [Voice Placement] > Edit
Breadcrumbs::for('placements.edit', static function ($trail, $singer, $placement) {
    $trail->parent('singers.show', $singer);
    $trail->push('Edit Voice Placement', route('placements.edit', [$singer, $placement]));
});

// Singers > Create
Breadcrumbs::for('singers.create', static function ($trail) {
    $trail->parent('singers.index');
    $trail->push('Create', route('singers.create'));
});

/**
 * SONGS
 */
// Songs
Breadcrumbs::for('songs.index', static function ($trail) {
    $trail->parent('dash');
    $trail->push('Songs', route('songs.index'));
});

// Songs > [Song]
Breadcrumbs::for('songs.show', static function ($trail, $song) {
    $trail->parent('songs.index');
    $trail->push($song->title, route('songs.show', $song));
});

// Songs > [Song] > Edit
Breadcrumbs::for('songs.edit', static function ($trail, $song) {
    $trail->parent('songs.show', $song);
    $trail->push('Edit', route('songs.edit', $song));
});

// Songs > Create
Breadcrumbs::for('songs.create', static function ($trail) {
    $trail->parent('songs.index');
    $trail->push('Create', route('songs.create'));
});
// Songs > Learning Mode
Breadcrumbs::for('songs.learning', static function ($trail) {
    $trail->parent('songs.index');
    $trail->push('Learning Mode', route('songs.learning'));
});


/**
 * EVENTS
 */
// Events
Breadcrumbs::for('events.index', static function ($trail) {
    $trail->parent('dash');
    $trail->push('Events', route('events.index'));
});

// Events > [Event]
Breadcrumbs::for('events.show', static function ($trail, $event) {
    $trail->parent('events.index');
    $trail->push($event->title, route('events.show', $event));
});

// Events > [Event] > Edit
Breadcrumbs::for('events.edit', static function ($trail, $event) {
    $trail->parent('events.show', $event);
    $trail->push('Edit', route('events.edit', $event));
});

// Events > Create
Breadcrumbs::for('events.create', static function ($trail) {
    $trail->parent('events.index');
    $trail->push('Create', route('events.create'));
});

/**
 * DOCUMENTS
 */
// Folders
Breadcrumbs::for('folders.index', static function ($trail) {
    $trail->parent('dash');
    $trail->push('Folders', route('folders.index'));
});

// Folders > [Folder]
Breadcrumbs::for('folders.show', static function ($trail, $folder) {
    $trail->parent('folders.index');
    $trail->push($folder->title, route('folders.show', $folder));
});

// Folders > [Folder] > Edit
Breadcrumbs::for('folders.edit', static function ($trail, $folder) {
    $trail->parent('folders.show', $folder);
    $trail->push('Edit', route('folders.edit', $folder));
});

// Folders > Create
Breadcrumbs::for('folders.create', static function ($trail) {
    $trail->parent('folders.index');
    $trail->push('Create', route('folders.create'));
});

/**
 * RISER STACKS
 */
// Riser Stacks
Breadcrumbs::for('stacks.index', static function ($trail) {
    $trail->parent('dash');
    $trail->push('Riser Stacks', route('stacks.index'));
});

// Riser Stacks > [Riser Stack]
Breadcrumbs::for('stacks.show', static function ($trail, $stack) {
    $trail->parent('stacks.index');
    $trail->push($stack->title, route('stacks.show', $stack));
});

// Riser Stacks > [Riser Stack] > Edit
Breadcrumbs::for('stacks.edit', static function ($trail, $stack) {
    $trail->parent('stacks.show', $stack);
    $trail->push('Edit', route('stacks.edit', $stack));
});

// Riser Stacks > Create
Breadcrumbs::for('stacks.create', static function ($trail) {
    $trail->parent('stacks.index');
    $trail->push('Create', route('stacks.create'));
});

/**
 * MAILING LISTS
 */
// Mailing Lists
Breadcrumbs::for('groups.index', static function ($trail) {
    $trail->parent('dash');
    $trail->push('Mailing Lists', route('groups.index'));
});

// Mailing Lists > [Mailing List]
Breadcrumbs::for('groups.show', static function ($trail, $group) {
    $trail->parent('groups.index');
    $trail->push($group->title, route('groups.show', $group));
});

// Mailing Lists > [Mailing List] > Edit
Breadcrumbs::for('groups.edit', static function ($trail, $group) {
    $trail->parent('groups.show', $group);
    $trail->push('Edit', route('groups.edit', $group));
});

// Mailing Lists > Create
Breadcrumbs::for('groups.create', static function ($trail) {
    $trail->parent('groups.index');
    $trail->push('Create', route('groups.create'));
});

/**
 * TASKS
 */
// Tasks
Breadcrumbs::for('tasks.index', static function ($trail) {
    $trail->parent('dash');
    $trail->push('Tasks', route('tasks.index'));
});

// Tasks > [Task]
Breadcrumbs::for('tasks.show', static function ($trail, $task) {
    $trail->parent('tasks.index');
    $trail->push($task->name, route('tasks.show', $task));
});

// Tasks > Create
Breadcrumbs::for('tasks.create', static function ($trail) {
    $trail->parent('tasks.index');
    $trail->push('Create', route('tasks.create'));
});


/**
 * TEMPLATES
 */
// Templates
Breadcrumbs::for('notification-templates.index', static function ($trail) {
    $trail->parent('dash');
    $trail->push('Notification Templates', route('notification-templates.index'));
});
