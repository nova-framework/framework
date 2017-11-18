<?php

namespace App\Modules\Content\Controllers;

use Nova\Http\Request;
use Nova\Routing\Controller as BaseController;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use Shared\Support\ReCaptcha;

use App\Modules\Content\Models\Comment;
use App\Modules\Content\Models\Post;
use App\Modules\Content\Notifications\CommentSubmitted as CommentSubmittedNotification;


class Comments extends BaseController
{

    protected function validator(array $data)
    {
        $rules = array(
            'comment_author'        => 'required',
            'comment_author_email'  => 'required|email',
            'comment_author_url'    => 'sometimes|required',
            'comment_content'       => 'required'
        );

        $attributes = array(
            'comment_author'       => __d('content', 'Name'),
            'comment_author_email' => __d('content', 'Email Address'),
            'comment_author_url'   => __d('content', 'Website'),
            'comment_content'      => __d('content', 'Message'),
        );

        return Validator::make($data, $rules, array(), $attributes);
    }

    public function store(Request $request, $id)
    {
        $input = $request->all();

        // Verify the submitted reCAPTCHA
        if (! Auth::check() && ! ReCaptcha::check($request->input('g-recaptcha-response'), $request->ip())) {
            return Redirect::back()->withInput($input)->withStatus(__d('content', 'The reCaptcha verification failed.'), 'danger');
        }

        try {
            $post = Post::where('type', 'post')->findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Post not found: #{0}', $id), 'danger');
        }

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return Redirect::back()->withInput($input)->withErrors($validator);
        }

        $userId = Auth::id() ?: 0;

        $comment = Comment::create(array(
            'post_id'      => $post->id,
            'author'       => $input['comment_author'],
            'author_email' => $input['comment_author_email'],
            'author_url'   => $input['comment_author_url'],
            'author_ip'    => $request->ip(),
            'content'      => $input['comment_content'],
            'approved'     => 0,
            'user_id'      => $userId,
        ));

        if (! is_null($userId) && ($userId === $post->author->id)) {
            // DO not send a notication to yourself.
        } else {
            $post->author->notify(new CommentSubmittedNotification($comment, $post));
        }

        // Update the comments count in the parent Post.
        $post->updateCommentCount();

        // Invalidate the parent Post cache.
        Cache::forget('content.posts.' .$post->name);

        return Redirect::back()
            ->withStatus(__d('content', 'Your comment is waiting approval.'), 'success');
    }
}
