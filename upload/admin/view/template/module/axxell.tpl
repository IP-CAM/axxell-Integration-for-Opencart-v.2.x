<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-axxell" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-axxell"
                      class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-apiurl"><?php echo $entry_apiurl; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="axxell_apiurl" value="<?php echo $axxell_apiurl; ?>"
                                   placeholder="<?php echo $entry_apiurl; ?>" id="input-apiurl"
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-accesskey"><?php echo $entry_accesskey; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="axxell_accesskey" value="<?php echo $axxell_accesskey; ?>"
                                   placeholder="<?php echo $entry_accesskey; ?>" id="input-accesskey"
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-secretkey"><?php echo $entry_secretkey; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="axxell_secretkey" value="<?php echo $axxell_secretkey; ?>"
                                   placeholder="<?php echo $entry_secretkey; ?>" id="input-secretkey"
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-push-catalog"><?php echo $entry_push_catalog; ?></label>
                        <div class="col-sm-10">
                            <input type="checkbox" name="axxell_push_catalog" value="yes"
                                   id="input-push-catalog"
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="axxell_status" id="input-status" class="form-control">
                                <?php if ($axxell_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        <!--
        //-->
    </script>
</div>
<?php echo $footer; ?>