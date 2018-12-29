<div class="<?php echo e($viewClass['form-group']); ?> <?php echo !$errors->has($column) ?: 'has-error'; ?>">

    <label for="<?php echo e($id); ?>" class="<?php echo e($viewClass['label']); ?> control-label"><?php echo e($label); ?></label>

    <div class="<?php echo e($viewClass['field']); ?>" id="<?php echo e($id); ?>">

        <?php echo $__env->make('admin::form.error', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!$inline): ?><div class="checkbox"><?php endif; ?>
            <label <?php if($inline): ?>class="checkbox-inline"<?php endif; ?>>
                <input type="checkbox" name="<?php echo e($name); ?>[]" value="<?php echo e($option); ?>" class="<?php echo e($class); ?>" <?php echo e(in_array($option, (array)old($column, $value)) || ($value === null && in_array($label, $checked)) ?'checked':''); ?> <?php echo $attributes; ?> />&nbsp;<?php echo e($label); ?>&nbsp;&nbsp;
            </label>
            <?php if(!$inline): ?></div><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <input type="hidden" name="<?php echo e($name); ?>[]">

        <?php echo $__env->make('admin::form.help-block', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    </div>
</div>
