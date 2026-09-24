<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait RespondsAjax
{
    /**
     * Balas JSON {ok, message, redirect?} untuk request AJAX
     * (form data-ajax), redirect biasa otherwise.
     * Flash session hanya bila ada redirect (toast meng-cover sisanya).
     */
    protected function ajaxOrRedirect(Request $request, string $route, string $message, array $extra = [])
    {
        if ($request->wantsJson() || $request->ajax()) {
            if (isset($extra['redirect'])) {
                session()->flash('success', $message);
            }

            return response()->json(array_merge(['ok' => true, 'message' => $message], $extra));
        }

        return redirect()->route($route)->with('success', $message);
    }

    /** Kembalikan partial HTML (JSON) untuk filter AJAX, full view otherwise. */
    protected function ajaxPartial(Request $request, string $partial, array $data, string $view)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'html' => view($partial, $data)->render()]);
        }

        return view($view, $data);
    }
}
