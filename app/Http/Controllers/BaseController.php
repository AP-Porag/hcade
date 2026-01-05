<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class BaseController extends Controller
{
    protected function render(string $view, array $data = [])
    {
        return Inertia::render($view, $data);
    }

    protected function success(string $message)
    {
        return redirect()->back()->with('success', $message);
    }

    protected function error(string $message)
    {
        return redirect()->back()->with('error', $message);
    }
}
