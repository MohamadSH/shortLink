<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function generateShortLink(Request $request)
    {
        $this->validateLinkGenerationRequest($request);

        $link = $request->get('link');

        $key = Str::random(6);
        Cache::put($key , $link );

        $shortLink = env('APP_URL').'/s/'.$key ;

        return response()->json([
            'data'  => [
                'short_link'  => $shortLink,
            ]
        ]);
    }

    public function redirect($key)
    {
        $link  = Cache::get($key);

        $clickCountKey = $key.':clickCount:';
        $clickCount = Cache::get($clickCountKey);
        if(!isset($clickCount)){
            $clickCount = 1;
        }
        Cache::put($clickCountKey , $clickCount );

        return redirect($link);
    }

    /**
     * @param Request $request
     *
     * @throws ValidationException
     */
    private function validateLinkGenerationRequest(Request $request): void
    {
        $this->validate($request, [
            'link' => 'required',
        ]);
    }
}
