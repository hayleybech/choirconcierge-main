<?php

namespace App\Http\Controllers;

use App\Models\NotificationTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use League\CommonMark\CommonMarkConverter;

class NotificationTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $templates = NotificationTemplate::all();

        foreach($templates as $template) {
            $template->body_rendered = (new CommonMarkConverter())->convertToHtml($template->body);
        }
		
		return view('notification-templates.index', compact('templates'));
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
     * @param NotificationTemplate $notificationTemplate
     * @return View
     */
    public function show(NotificationTemplate $notificationTemplate): View
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param NotificationTemplate $notificationTemplate
     * @return View
     */
    public function edit(NotificationTemplate $notificationTemplate): View
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Models\NotificationTemplate $notificationTemplate
     * @return RedirectResponse
     */
    public function update(Request $request, NotificationTemplate $notificationTemplate): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param NotificationTemplate $notificationTemplate
     * @return RedirectResponse
     */
    public function destroy(NotificationTemplate $notificationTemplate): RedirectResponse
    {
        //
    }
}
