<div class="box-footer">

    <?php echo e(csrf_field()); ?>


    <div class="col-md-<?php echo e($width['label']); ?>">
    </div>

    <div class="col-md-<?php echo e($width['field']); ?>">

        <?php if(in_array('submit', $buttons)): ?>
        <div class="btn-group pull-right">
            <button type="submit" class="btn btn-primary"><?php echo e(trans('admin.submit')); ?></button>
        </div>

        <?php if(in_array('continue_editing', $checkboxes)): ?>
        <label class="pull-right" style="margin: 5px 10px 0 0;">
            <input type="checkbox" class="after-submit" name="after-save" value="1"> <?php echo e(trans('admin.continue_editing')); ?>

        </label>
        <?php endif; ?>

        <?php if(in_array('continue_creating', $checkboxes)): ?>
            <label class="pull-right" style="margin: 5px 10px 0 0;">
                <input type="checkbox" class="after-submit" name="after-save" value="2"> <?php echo e(trans('admin.continue_creating')); ?>

            </label>
        <?php endif; ?>

        <?php if(in_array('view', $checkboxes)): ?>
        <label class="pull-right" style="margin: 5px 10px 0 0;">
            <input type="checkbox" class="after-submit" name="after-save" value="3"> <?php echo e(trans('admin.view')); ?>

        </label>
        <?php endif; ?>

        <?php endif; ?>

        <?php if(in_array('reset', $buttons)): ?>
        <div class="btn-group pull-left">
            <button type="reset" class="btn btn-warning"><?php echo e(trans('admin.reset')); ?></button>
        </div>
        <?php endif; ?>
    </div>
</div>