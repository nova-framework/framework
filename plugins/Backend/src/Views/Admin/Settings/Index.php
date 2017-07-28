<div class="row">
    <h1><?= __d('backend', 'Settings'); ?></h1>
    <ol class="breadcrumb">
        <li><a href='<?= site_url('admin/dashboard'); ?>'><i class="fa fa-dashboard"></i> <?= __d('backend', 'Dashboard'); ?></a></li>
        <li><?= __d('backend', 'Settings'); ?></li>
    </ol>
</div>

<?= View::fetch('Partials/Messages'); ?>

<!-- Main content -->
<div class="row">

<?php if (CONFIG_STORE == 'database') { ?>

<form class="form-horizontal" action="<?= site_url('admin/settings'); ?>" method="POST">

<div class="panel panel-default">
    <div class="panel-body" style="padding-top: 0;">
        <h3><?= __d('backend', 'Site Settings'); ?></h3>
        <hr style="margin-top: 0;">

        <div class="form-group">
            <label class="col-sm-4 control-label" for="sitename"><?= __d('backend', 'Site Name'); ?></label>
            <div class="col-sm-8">
                <input name="siteName" id="siteName" type="text" class="form-control" value="<?= $options['siteName']; ?>">
            </div>
        </div>

        <hr style="margin-bottom: 15px;">
        <input class="btn btn-success col-sm-2 pull-right" type="submit" id="submit" name="submit" value="<?= __d('backend', 'Apply the changes') ?>" />
        <div class="clearfix"></div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body" style="padding-top: 0;">
        <h3><?= __d('backend', 'Mailer Settings'); ?></h3>
        <hr style="margin-top: 0;">

        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailDriver"><?= __d('backend', 'Mail Driver'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-3" style="padding: 0;">
                    <select name="mailDriver" id="mailDriver" class="form-control">
                        <option value="smtp" <?php if ($options['mailDriver'] == 'smtp') { echo "selected='selected'"; }?>><?= __d('backend', 'SMTP'); ?></option>
                        <option value="mail" <?php if ($options['mailDriver'] == 'mail') { echo "selected='selected'"; }?>><?= __d('backend', 'Mail'); ?></option>
                        <option value="sendmail" <?php if ($options['mailDriver'] == 'sendmail') { echo "selected='selected'"; }?>><?= __d('backend', 'Sendmail'); ?></option>
                    </select>
                </div>
                <div class='clearfix'></div>
                <small><?= __d('backend', 'Whether or not is used a external SMTP Server to send the E-mails.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailFromAddress"><?= __d('backend', 'E-mail From Address'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailFromAddress" id="mailFromAddress" type="text" class="form-control" value="<?= $options['mailFromAddress']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('backend', 'The outgoing E-mail address.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailFromName"><?= __d('backend', 'E-mail From Name'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailFromName" id="mailFromName" type="text" class="form-control" value="<?= $options['mailFromName']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('backend', 'The From Field of any outgoing e-mails.'); ?></small>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailHost"><?= __d('backend', 'Server Name'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailHost" id="mailHost" type="text" class="form-control" value="<?= $options['mailHost']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('backend', 'The name of the external SMTP Server.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailPort"><?= __d('backend', 'Server Port'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-2" style="padding: 0;">
                    <input name="mailPort" id="mailPort" type="text" class="form-control" value="<?= $options['mailPort']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('backend', 'The Port used for connecting to the external SMTP Server.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailEncryption"><?= __d('backend', 'Use the Cryptography'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-2" style="padding: 0;">
                    <select name="mailEncryption" id="mailEncryption" class="form-control">
                        <option value="ssl" <?php if ($options['mailEncryption'] == 'ssl') { echo "selected='selected'"; }?>>SSL</option>
                        <option value="tls" <?php if ($options['mailEncryption'] == 'tls') { echo "selected='selected'"; }?>>TLS</option>
                        <option value="" <?php if ($options['mailEncryption'] == '') { echo "selected='selected'"; }?>><?= __d('backend', 'NONE'); ?></option>
                    </select>
                </div>
                <div class='clearfix'></div>
                <small><?= __d('backend', 'Whether or not is used the Cryptography for connecting to the external SMTP Server.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailUsername"><?= __d('backend', 'Server Username'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailUsername" id="mailUsername" type="text" class="form-control" value="<?= $options['mailUsername']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('backend', 'The Username used to connect to the external SMTP Server.'); ?></small>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="mailPassword"><?= __d('backend', 'Server Password'); ?></label>
            <div class="col-sm-8">
                <div class="col-sm-6" style="padding: 0;">
                    <input name="mailPassword" id="mailPassword" type="password" class="form-control" value="<?= $options['mailPassword']; ?>">
                </div>
                <div class='clearfix'></div>
                <small><?= __d('backend', 'The Password used to connect to the external SMTP Server.'); ?></small>
            </div>
        </div>

        <hr style="margin-bottom: 15px;">
        <input class="btn btn-success col-sm-2 pull-right" type="submit" id="submit" name="submit" value="<?= __d('backend', 'Apply the changes') ?>" />
        <div class="clearfix"></div>
    </div>
</div>

<input type="hidden" name="_token" value="<?= csrf_token(); ?>" />

</form>

<?php } else { ?>

<div class="alert alert-info">
    <?= __d('backend', 'The Settings are not available while the Config Store is on Files Mode.'); ?>
</div>

<?php } ?>

</div>
