<?php

/**
 * WELCOME
 */
Breadcrumbs::for('welcome', static function($trail){
    $trail->push('Welcome', route('menu'));
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

// Singers > Create
Breadcrumbs::for('singers.create', static function ($trail) {
    $trail->parent('singers.index');
    $trail->push('Create', route('singers.create'));
});
