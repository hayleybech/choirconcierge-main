<?php

namespace App\Http\Controllers\Central;

use App\CustomSorts\UserNameSort;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Tenant::class);

        $pagination = $this->getUsers();

        return Inertia::render('Central/Users/Index', [
            'users' => $pagination
                ->getCollection(),
            'pagination' => $pagination,
        ]);
    }

    public function show() {
        $this->authorize('viewAny', Tenant::class);
    }

    private function getUsers() {
        $nameSort = AllowedSort::custom('full-name', new UserNameSort(), 'name');

        return QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::callback('search', fn(Builder $query, $value) => $query
                    ->whereRaw('CONCAT(first_name, ?, last_name) LIKE LOWER(?)', [' ', "%$value%"])
                    ->orWhereRaw('email LIKE LOWER(?)', ["%$value%"])
                ),
            ])
            ->defaultSort($nameSort)
            ->allowedSorts([
                $nameSort,
                'email',
            ])
            ->with('memberships.tenant')
            ->paginate(50)->appends(request()->query());
    }
}
