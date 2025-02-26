<?php

/**
 * WELCOME
 */
// Home
Breadcrumbs::for('menu', static function ($trail) {
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
Breadcrumbs::for('dash', static function ($trail) {
    $trail->push('Dashboard', route('dash'));
});

/**
 * ACCOUNT SETTINGS
 */
// Edit Account Settings
Breadcrumbs::for('accounts.edit', static function ($trail) {
    $trail->parent('dash');
    $trail->push('Edit Account Settings', route('accounts.edit'));
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
    $trail->push($singer->user->name, route('singers.show', $singer));
});

// Singers > [Singer] > Edit
Breadcrumbs::for('singers.edit', static function ($trail, $singer) {
    $trail->parent('singers.show', $singer);
    $trail->push('Edit', route('singers.edit', $singer));
});

// Singers > [Singer] > Create Voice Placement
Breadcrumbs::for('singers.placements.create', static function ($trail, $singer) {
    $trail->parent('singers.show', $singer);
    $trail->push('Create Voice Placement', route('singers.placements.create', $singer));
});
// Singers > [Singer] > [Voice Placement] > Edit
Breadcrumbs::for('singers.placements.edit', static function ($trail, $singer, $placement) {
    $trail->parent('singers.show', $singer);
    $trail->push('Edit Voice Placement', route('singers.placements.edit', [$singer, $placement]));
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

// Songs > [Song] > Learning
Breadcrumbs::for('songs.singers.index', static function ($trail, $song) {
    $trail->parent('songs.show', $song);
    $trail->push('Learning', route('songs.singers.index', $song));
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

// Events > [Event] > Attendances
Breadcrumbs::for('events.attendances.index', static function ($trail, $event) {
    $trail->parent('events.show', $event);
    $trail->push('Attendance', route('events.attendances.index', $event));
});

// Events > Reports > Attendance
Breadcrumbs::for('events.reports.attendance', static function ($trail) {
    $trail->parent('events.index');
    $trail->push('Reports');
    $trail->push('Attendance', route('events.reports.attendance'));
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
    $trail->push('Onboarding', route('tasks.index'));
});

// Tasks > [Task]
Breadcrumbs::for('tasks.show', static function ($trail, $task) {
    $trail->parent('tasks.index');
    $trail->push($task->name, route('tasks.show', $task));
});

// Tasks > [Task] > Add Notification
Breadcrumbs::for('tasks.notifications.create', static function ($trail, $task) {
    $trail->parent('tasks.show', $task);
    $trail->push('Add Notification', route('tasks.notifications.store', $task));
});

// Tasks > [Task] > [Notification]
Breadcrumbs::for('tasks.notifications.show', static function ($trail, $task, $notification) {
    $trail->parent('tasks.show', $task);
    $trail->push($notification->subject, route('tasks.notifications.show', [$task, $notification]));
});

// Tasks > [Task] > [Notification] > Edit
Breadcrumbs::for('tasks.notifications.edit', static function ($trail, $task, $notification) {
    $trail->parent('tasks.notifications.show', $task, $notification);
    $trail->push('Edit', route('tasks.notifications.edit', [$task, $notification]));
});

// Tasks > Create
Breadcrumbs::for('tasks.create', static function ($trail) {
    $trail->parent('tasks.index');
    $trail->push('Add Task', route('tasks.create'));
});

/**
 * VOICE PARTS
 */

// Singers > Voice Parts
Breadcrumbs::for('voice-parts.index', static function ($trail) {
    $trail->parent('singers.index');
    $trail->push('Voice Parts', route('voice-parts.index'));
});
// Singers > Voice Parts > Create
Breadcrumbs::for('voice-parts.create', static function ($trail) {
    $trail->parent('voice-parts.index');
    $trail->push('Create', route('voice-parts.create'));
});
// Singers > Voice Parts > [Voice Part]
Breadcrumbs::for('voice-parts.show', static function ($trail, $voice_part) {
    $trail->parent('voice-parts.index');
    $trail->push($voice_part->title, route('voice-parts.show', $voice_part));
});
// Singers > Voice Parts > [Voice Part] > Edit
Breadcrumbs::for('voice-parts.edit', static function ($trail, $voice_part) {
    $trail->parent('voice-parts.show', $voice_part);
    $trail->push('Edit', route('voice-parts.edit', $voice_part));
});

/**
 * ROLES
 */

// Singers > Roles
Breadcrumbs::for('roles.index', static function ($trail) {
    $trail->parent('singers.index');
    $trail->push('Roles', route('roles.index'));
});
// Singers > Roles > Create
Breadcrumbs::for('roles.create', static function ($trail) {
    $trail->parent('roles.index');
    $trail->push('Create', route('roles.create'));
});
// Singers > Roles > [Role]
Breadcrumbs::for('roles.show', static function ($trail, $role) {
    $trail->parent('roles.index');
    $trail->push($role->name, route('roles.show', $role));
});
// Singers > Roles > [Role] > Edit
Breadcrumbs::for('roles.edit', static function ($trail, $role) {
    $trail->parent('roles.show', $role);
    $trail->push('Edit', route('roles.edit', $role));
});
