<?php

namespace Modules\Content\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Cache;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;

use Modules\Content\Models\Comment;
use Modules\Platform\Controllers\Admin\BaseController;


class Comments extends BaseController
{

    protected function validator(array $data)
    {
        $rules = array(
            'author'       => 'required',
            'author_email' => 'required|email',
            'author_url'   => 'sometimes|required',
            'content'      => 'required'
        );

        return Validator::make($data, $rules);
    }

    public function index()
    {
        $comments = Comment::with('post')->orderBy('created_at', 'DESC')->paginate(10);

        return $this->createView()
            ->shares('title', __d('content', 'Comments'))
            ->with('comments', $comments);
    }

    public function load($id)
    {
        try {
            $comment = Comment::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Response::json(array('error' => 'Not Found'), 404);
        }

        return Response::json($comment->toArray(), 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        try {
            $comment = Comment::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Comment not found: #{0}', $id), 'danger');
        }

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withStatus($validator->errors(), 'danger');
        }

        $comment->author       = $input['author'];
        $comment->author_email = $input['author_email'];
        $comment->author_url   = $input['author_url'];
        $comment->content      = $input['content'];

        // Approve or unapprove the Comment.
        $comment->approved = (int) $request->has('approved');

        $comment->save();

        // Invalidate the parent Post cache.
        Cache::forget('content.posts.' .$comment->post->name);

        return Redirect::back()
            ->withStatus(__d('content', 'The Comment was successfully updated.'), 'success');
    }

    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Comment not found: #{0}', $id), 'danger');
        }

        $post = $comment->post()->first();

        // Delete the Comment.
        $comment->delete();

        // Update the comments count in the parent Post.
        $post->updateCommentCount();

        // Invalidate the parent Post cache.
        Cache::forget('content.posts.' .$post->name);

        return Redirect::back()
            ->withStatus(__d('content', 'The Comment was successfully deleted.'), 'success');
    }

    public function approve($id)
    {
        try {
            $comment = Comment::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Comment not found: #{0}', $id), 'danger');
        }

        $comment->approved = 1;

        $comment->save();

        // Invalidate the parent Post cache.
        Cache::forget('content.posts.' .$comment->post->name);

        return Redirect::back()->withStatus(__d('content', 'The Comment was approved.'), 'success');
    }

    public function unapprove($id)
    {
        try {
            $comment = Comment::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Comment not found: #{0}', $id), 'danger');
        }

        $comment->approved = 0;

        $comment->save();

        // Invalidate the parent Post cache.
        Cache::forget('content.posts.' .$comment->post->name);

        return Redirect::back()->withStatus(__d('content', 'The Comment was unapproved.'), 'success');
    }
}
