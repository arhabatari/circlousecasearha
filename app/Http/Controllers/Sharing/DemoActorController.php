<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Controllers\Controller;
use App\Models\Sharing\DemoActor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DemoActorController extends Controller
{
    public function index(): View
    {
        return view('sharing.demo', ['actors' => DemoActor::orderBy('name')->get()]);
    }

    public function switch(Request $request): RedirectResponse
    {
        $data = $request->validate(['actor_id' => ['required', 'integer', Rule::exists('circlo_sharing_actors', 'id')]]);
        $request->session()->regenerate();
        $request->session()->put('circlo_sharing_actor_id', (int) $data['actor_id']);

        return redirect()->route('sharing.transactions.index')->with('success', 'Demo student selected.');
    }

    public function end(Request $request): RedirectResponse
    {
        $request->session()->forget('circlo_sharing_actor_id');
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('sharing.demo')->with('notice', 'Demo session ended.');
    }
}
