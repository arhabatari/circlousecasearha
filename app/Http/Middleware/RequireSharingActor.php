<?php

namespace App\Http\Middleware;

use App\Models\Sharing\DemoActor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSharingActor
{
    public function handle(Request $request, Closure $next): Response
    {
        $actor = DemoActor::find($request->session()->get('circlo_sharing_actor_id'));

        if (! $actor) {
            $request->session()->forget('circlo_sharing_actor_id');

            return redirect()->route('sharing.demo')->with('notice', 'Choose a demo student to continue.');
        }

        $request->attributes->set('sharing_actor', $actor);
        view()->share('actor', $actor);

        return $next($request);
    }
}
