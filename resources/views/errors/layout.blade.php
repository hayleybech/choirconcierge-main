<!DOCTYPE html>
<html lang="en" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: Lato, Arial, Helvetica, sans-serif;
            }
        </style>
    </head>
    <body class="h-full">
    <main class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
        <div class="text-center">
            <p class="text-base font-semibold text-purple-600">@yield('code') Error</p>
            <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">@yield('title')</h1>
            <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">@yield('message')</p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="https://www.choirconcierge.com" class="rounded-md bg-purple-600 px-3.5 py-2.5 text-sm text-white shadow-xs hover:bg-purple-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600">Go to our website</a>
                <a href="mailto:hello@choirconcierge.com" class="text-sm font-semibold text-gray-900">Email Us <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
    </main>
    </body>
</html>
