<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return RedirectResponse
     */
    public function create(): RedirectResponse
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Notification $notification
     * @return View
     */
    public function show(Notification $notification): View
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Notification $notification
     * @return View
     */
    public function edit(Notification $notification): View
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Notification $notification
     * @return RedirectResponse
     */
    public function update(Request $request, Notification $notification): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Notification $notification
     * @return RedirectResponse
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        //
    }
}
