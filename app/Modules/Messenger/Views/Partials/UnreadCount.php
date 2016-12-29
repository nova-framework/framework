<?php $count = Auth::user()->newMessagesCount(); ?>
<?php if($count > 0) { ?>
<span class="label label-danger"><?= $count; ?></span>
<?php } ?>
