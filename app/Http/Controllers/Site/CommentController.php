<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Modules\Post\Entities\Comment;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class CommentController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'comment'       => 'required|string',
            'post_id'       => 'required|integer',
        ]);

        $data               = $request->except('_token');
        $data['user_id']    = Sentinel::getUser()->id;

        $comment            = new Comment();
        $comment->fill($data);
        $comment->save();

        return redirect()->back();
    }

    public function saveReply(Request $request)
    {
        $request->validate([
            'comment'       => 'required|string',
            'post_id'       => 'required|integer',
            'comment_id'    => 'required|integer',
        ]);

        $data               = $request->except('_token');
        $data['user_id']    = Sentinel::getUser()->id;

        $comment            = new Comment();

        $comment->fill($data);
        $comment->save();

        return redirect()->back()->with('success');
    }

    public function switchLanguage(Request $request)
    {
        $lang               = $request->lang;

        App::setLocale($lang);
        Session::put('locale', $lang);
        getLocale($lang);

        Cache::Flush();

        $url                = URL::to('/').'/'.App::getLocale();
        return response()->json($url);
    }

    public function modeChange()
    {
        $mode               = Session::get('mode');

        if($mode == 'sg-dark'):
            Session::put('mode', 'sg-light');
        else:
            Session::put('mode', 'sg-dark');
        endif;

        return response()->json('success');
    }
}
