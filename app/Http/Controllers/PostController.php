<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Post;

use PubNub\PubNub;
use PubNub\PNConfiguration;

use Log;

class PostController extends Controller
{
    public function getAllPost(Request $request) {
        $posts = Post::orderBy('id', 'desc')
                    ->get();

        return response()->json(['data' => $posts]);
    }

    public function createPost(Request $request) {
        $name = $request->get('name');
        $content = $request->get('content');

        if ($name && $content) {
            $post = Post::create([
                'name' => $name,
                'content' => $content,
            ]);

            // Send signal
            $pnconf = new PNConfiguration();

            $pnconf->setSubscribeKey("sub-c-5dab58da-848a-4a04-9e1f-29b962c15518");
            $pnconf->setPublishKey("pub-c-7fe263d8-78b2-43c8-a402-91de6449d9b9");
            $pnconf->setUuid("testUser1");

            $pubnub = new PubNub($pnconf);
            $pubnub->publish()
                    ->channel("chat")
                    ->message($content)
                    ->sync();

            return response()->json(['data' => $post]);
        }

        return response()->json(['error' => 'Invalid input!'], 401);
    }
}
