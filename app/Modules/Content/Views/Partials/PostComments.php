<?php $comments = $post->comments->sortBy('created_at'); ?>

<?php if (($comments->count() > 0) || ($post->comment_status == 'open')) { ?>
<h3><?= __d('content', 'Comments'); ?></h3>
<hr>
<?php } ?>

<?php if ($comments->count() > 0) { ?>
<?php $format = __d('content', '%B %d, %Y %R'); ?>

<ul>
    <?php foreach($comments as $comment) { ?>
    <?php if (Auth::check() && ($comment->user_id !== Auth::id()) && ($post->author_id != Auth::id())) continue; ?>
    <?php if (! Auth::check() && ($comment->approved != 1)) continue; ?>
    <li>
        <a rel="nofollow" style="font-weight: bold;" target="_blank" href="<?= urlencode($comment->author_url); ?>"><?= e($comment->author); ?></a> <span><?= __d('content', 'commented on <b>{0}</b>', $comment->updated_at->formatLocalized($format)); ?></span>
        <div class="comment-body" style="margin-top: 10px; margin-bottom: 25px;">
        <?= e($comment->content); ?>
        </div>
    </li>
    <?php } ?>
</ul>
<?php } else if ($post->comment_status == 'open') { ?>
<?= __d('content', 'Be the first to comment.'); ?>
<?php } ?>

<div class="clearfix"></div>
<br>
<br>

<?php if ($post->comment_status == 'open') { ?>

<h3><?= __d('content', 'Leave a Reply'); ?></h3>
<hr>

<div class="col-md-8 col-md-offset-2">

<form action="<?= site_url('content/' .$post->id .'/comment'); ?>" method="POST">

    <?= csrf_field() ?>
    <input type="hidden" name="post_id" value="<?= $post->id; ?>" />

    <div class="form-group<?= $errors->has('comment_author') ? ' has-error' : ''; ?>">
        <label for="comment_author"><?= __d('content', 'Name'); ?> *</label> <br/>
        <input type="text" name="comment_author" class="form-control" value="<?= Input::old('comment_author'); ?>" />

        <?php if ($errors->has('comment_author')) { ?>
            <span class="help-block">
                <strong><?= $errors->first('comment_author'); ?></strong>
            </span>
        <?php } ?>
    </div>
    <div class="form-group<?= $errors->has('comment_author_email') ? ' has-error' : ''; ?>">
        <label for="comment_author_email"><?= __d('content', 'Email Address'); ?> *</label> <br/>
        <input type="text" name="comment_author_email" class="form-control" value="<?= Input::old('comment_author_email'); ?>" />

        <?php if ($errors->has('comment_author_email')) { ?>
            <span class="help-block">
                <strong><?= $errors->first('comment_author_email'); ?></strong>
            </span>
        <?php } ?>
    </div>
    <div class="form-group">
        <label for="comment_author_url"><?= __d('content', 'Website'); ?></label> <br/>
        <input type="text" name="comment_author_url" class="form-control" value="<?= Input::old('comment_author_url'); ?>" />
    </div>
    <div class="form-group">
        <label for="comment_content"><?= __d('content', 'Message'); ?></label> <br/>
        <textarea cols="60" rows="6" class="form-control" name="comment_content"><?= Input::old('comment_content'); ?></textarea>
    </div>
    <?php if (! Auth::check() && (Config::get('reCaptcha.active') === true)) { ?>
    <div style="width: 304px; margin: 0 auto; display: block;">
        <div id="captcha" style="width: 304px; height: 78px;"></div>
    </div>
    <div class="clearfix"></div>
    <hr style="margin-top: 15px; margin-bottom: 15px;">
    <?php } ?>
    <div class="form-group">
        <input type="submit" class="btn btn-primary pull-right col-md-3" value="<?= __d('content', 'Submit Comment'); ?>" />
    </div>
</form>

</div>

<?php } ?>

<?php if (! Auth::check() && (Config::get('reCaptcha.active') === true)) { ?>

<script type="text/javascript">

var captchaCallback = function() {
    grecaptcha.render('captcha', {'sitekey' : '<?= Config::get('reCaptcha.siteKey'); ?>'});
};

</script>

<script src="//www.google.com/recaptcha/api.js?onload=captchaCallback&render=explicit&hl=<?= Language::code(); ?>" async defer></script>

<?php } ?>
