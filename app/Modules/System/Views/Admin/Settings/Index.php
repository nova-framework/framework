<section class="content-header">
    <h1><?= __d('system', 'Settings'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('system', 'Dashboard'); ?></a></li>
        <li><?= __d('system', 'Settings'); ?></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

<?= Session::getMessages(); ?>

<?php if (CONFIG_STORE == 'database') { ?>

<form name='myForm' class="form-horizontal" action="<?= site_url('admin/settings'); ?>" method="POST">

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('system', 'Site Settings'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label class="col-sm-4 control-label" for="sitename"><?= __d('system', 'Site Name'); ?></label>
            <div class="col-sm-8">
                <input name="siteName" id="siteName" type="text" class="form-control" value="<?= $options['siteName']; ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="site_skin"><?= __d('system', 'Backend Skin'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-3" style="padding: 0;">
                    <select name="siteSkin" id="siteSkin" class="form-control">
                        <option value="blue"         <?php if ($options['siteSkin'] == 'blue')         { echo "selected='selected'"; } ?> ><?= __d('system', 'Blue'); ?></option>
                        <option value="blue-light"   <?php if ($options['siteSkin'] == 'blue-light')   { echo "selected='selected'"; } ?> ><?= __d('system', 'Blue Light'); ?></option>
                        <option value="black"        <?php if ($options['siteSkin'] == 'black')        { echo "selected='selected'"; } ?> ><?= __d('system', 'Black'); ?></option>
                        <option value="black-light"  <?php if ($options['siteSkin'] == 'black-light')  { echo "selected='selected'"; } ?> ><?= __d('system', 'Black Light'); ?></option>
                        <option value="purple"       <?php if ($options['siteSkin'] == 'purple')       { echo "selected='selected'"; } ?> ><?= __d('system', 'Purple'); ?></option>
                        <option value="purple-light" <?php if ($options['siteSkin'] == 'purple-light') { echo "selected='selected'"; } ?> ><?= __d('system', 'Purple Light'); ?></option>
                        <option value="yellow"       <?php if ($options['siteSkin'] == 'yellow')       { echo "selected='selected'"; } ?> ><?= __d('system', 'Yellow'); ?></option>
                        <option value="yellow-light" <?php if ($options['siteSkin'] == 'yellow-light') { echo "selected='selected'"; } ?> ><?= __d('system', 'Yellow Light'); ?></option>
                        <option value="red"          <?php if ($options['siteSkin'] == 'red')          { echo "selected='selected'"; } ?> ><?= __d('system', 'Red'); ?></option>
                        <option value="red-light"    <?php if ($options['siteSkin'] == 'red-light')    { echo "selected='selected'"; } ?> ><?= __d('system', 'Red Light'); ?></option>
                        <option value="green"        <?php if ($options['siteSkin'] == 'green')        { echo "selected='selected'"; } ?> ><?= __d('system', 'Green'); ?></option>
                        <option value="green-light"  <?php if ($options['siteSkin'] == 'green-light')  { echo "selected='selected'"; } ?> ><?= __d('system', 'Green Light'); ?></option>
                    </select>
                </div>
                <div class='clearfix'></div>
                <small><?= __d('system', 'The Skin used by the Site\'s Template.'); ?></small>
            </div>
        </div>
    </div>
</div>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __d('system', 'Mailer Settings'); ?></h3>
    </div>
    <div class="box-body">
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailDriver"><?= __d('system', 'Mail Driver'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-3" style="padding: 0;">
                    <select name="mailDriver" id="mailDriver" class="form-control">
                        <option value="smtp" <?php if ($options['mailDriver'] == 'smtp') { echo "selected='selected'"; }?>><?= __d('system', 'SMTP'); ?></option>
                        <option value="mail" <?php if ($options['mailDriver'] == 'mail') { echo "selected='selected'"; }?>><?= __d('system', 'Mail'); ?></option>
                        <option value="sendmail" <?php if ($options['mailDriver'] == 'sendmail') { echo "selected='selected'"; }?>><?= __d('system', 'Sendmail'); ?></option>
                    </select>
                </div>
                <div class='clearfix'></div>
                <small><?= __d('system', 'Whether or not is used a external SMTP Server to send the E-mails.'); ?></small>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailFromAddress"><?= __d('system', 'E-mail From Address'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailFromAddress" id="mailFromAddress" type="text" class="form-control" value="<?= $options['mailFromAddress']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('system', 'The outgoing E-mail address.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailFromName"><?= __d('system', 'E-mail From Name'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailFromName" id="mailFromName" type="text" class="form-control" value="<?= $options['mailFromName']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('system', 'The From Field of any outgoing e-mails.'); ?></small>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailHost"><?= __d('system', 'Server Name'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailHost" id="mailHost" type="text" class="form-control" value="<?= $options['mailHost']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('system', 'The name of the external SMTP Server.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailPort"><?= __d('system', 'Server Port'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-2" style="padding: 0;">
                    <input name="mailPort" id="mailPort" type="text" class="form-control" value="<?= $options['mailPort']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('system', 'The Port used for connecting to the external SMTP Server.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailEncryption"><?= __d('system', 'Use the Cryptography'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-2" style="padding: 0;">
                    <select name="mailEncryption" id="mailEncryption" class="form-control">
                        <option value="ssl" <?php if ($options['mailEncryption'] == 'ssl') { echo "selected='selected'"; }?>>SSL</option>
                        <option value="tls" <?php if ($options['mailEncryption'] == 'tls') { echo "selected='selected'"; }?>>TLS</option>
                        <option value="" <?php if ($options['mailEncryption'] == '') { echo "selected='selected'"; }?>><?= __d('system', 'NONE'); ?></option>
                    </select>
                </div>
                <div class='clearfix'></div>
                <small><?= __d('system', 'Whether or not is used the Cryptography for connecting to the external SMTP Server.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailUsername"><?= __d('system', 'Server Username'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailUsername" id="mailUsername" type="text" class="form-control" value="<?= $options['mailUsername']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('system', 'The Username used to connect to the external SMTP Server.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailPassword"><?= __d('system', 'Server Password'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailPassword" id="mailPassword" type="password" class="form-control" value="<?= $options['mailPassword']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('system', 'The Password used to connect to the external SMTP Server.'); ?></small>
            </div>
        </div>
    </div>
</div>

<div class="box box-widget">
    <div class="box-body">
        <div class="col-lg-12" style="padding-right: 0;">
            <input class="btn btn-success col-sm-3 pull-right" type="submit" id="submit" name="submit" value="<?= __d('system', 'Apply the changes') ?>" />&nbsp;
        </div>
    </div>
</div>

<input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />

</form>

<?php } else { ?>

<div class="callout callout-info">
    <?= __d('system', 'The Settings are not available while the Config Store is on Files Mode.'); ?>
</div>

<?php } ?>

</section>

