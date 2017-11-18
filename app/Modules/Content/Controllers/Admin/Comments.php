<?php

namespace App\Modules\Content\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Http\Request;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Response;
use Nova\Support\Facades\Validator;

use App\Modules\Content\Models\Comment;
use App\Modules\Platform\Controllers\Admin\BaseController;


class Comments extends BaseController
{

    public function index()
    {
        $comments = Comment::with('post')->paginate(15);

        return $this->createView()
            ->shares('title', __d('content', 'Comments'))
            ->with('comments', $comments);
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

        return Redirect::back()->withStatus(__d('content', 'The Comment <b>{0}</b> was approved', $id), 'success');
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

        return Redirect::back()->withStatus(__d('content', 'The Comment <b>{0}</b> was unapproved', $id), 'success');
    }

    public function load($id)
    {
        try {
            $comment = Comment::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Response::json(array('error' => 'Not Found'), 400);
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

        $validator = Validator::make($input, array(
            'comment_author'        => 'required',
            'comment_author_email'  => 'required|email',
            'comment_author_url'    => 'sometimes|required',
            'comment_content'       => 'required'
        ));

        if ($validator->fails()) {
            return Redirect::back()->withInput($input)->withErrors($validator->errors());
        }

        $comment->author       = $input['author'];
        $comment->author_email = $input['author_email'];
        $comment->author_url   = $input['author_url'];
        $comment->content      = $input['content'];

        // Approve or unapprove the Comment.
        $comment->approved = (int) $request->has('approved');

        $comment->save();

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

        $comment->delete();

        return Redirect::back()
            ->withStatus(__d('content', 'The Comment was successfully deleted.'), 'success');
    }
}
