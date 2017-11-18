<?php

namespace App\Modules\Content\Controllers;

use Nova\Http\Request;
use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Redirect;
use Nova\Support\Facades\Validator;

use App\Modules\Content\Models\Comment;
use App\Modules\Platform\Controllers\BaseController;


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

        return Validator::make($data, $rules);
    }

    public function store(Request $request, $id)
    {
        $input = $request->all();

        $validator = $this->validator($input);

        if ($validator->fails()) {
            return Redirect::back()->withInput($input)->withErrors($validator->errors());
        }

        $comment = Comment::create(array(
            'post_id'      => $input['post_id'],
            'author'       => $input['comment_author'],
            'author_email' => $input['comment_author_email'],
            'author_url'   => $input['comment_author_url'],
            'author_ip'    => $request->ip(),
            'content'      => $input['comment_content'],
            'approved'     => 0,
            'user_id'      => Auth::id(),
        ));

        return Redirect::back()
            ->withStatus(__d('content', 'Your comment is waiting approval.'), 'success');
    }
}
