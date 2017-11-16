<div id="custom-search-input" style="margin-top: 15px; margin-bottom: 40px;">
    <form id="content-search-form" action="<?= site_url('content/search'); ?>" method='GET' role="form">

    <div class="input-group col-md-12">
        <input name="query" type="text" class="form-control input-lg" placeholder="<?= __d('content', 'Search ...'); ?>" />
        <span class="input-group-btn">
            <button class="btn btn-info btn-lg" type="button">
                <i class="glyphicon glyphicon-search"></i>
            </button>
        </span>
    </div>

    </form>
</div>
