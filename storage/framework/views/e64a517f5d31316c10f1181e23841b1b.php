<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Register')); ?>

<?php $__env->stopSection(); ?>
<?php
    if ($lang == 'ar' || $lang == 'he') {
        $setting['SITE_RTL'] = 'on';
    }
    $lang = \App::getLocale('lang');
    $LangName = \App\Models\Languages::where('code', $lang)->first();
    if (empty($LangName)) {
        $LangName = new App\Models\Utility();
        $LangName->fullName = 'English';
    }
    $settings = \App\Models\Utility::settings();
    $user = \Auth::user();
    $landingPageSettings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
    $keyArray = [];
    if (
        is_array(json_decode($landingPageSettings['menubar_page'])) ||
        is_object(json_decode($landingPageSettings['menubar_page']))
    ) {
        foreach (json_decode($landingPageSettings['menubar_page']) as $key => $value) {
            if (
                in_array($value->menubar_page_name, ['Terms and Conditions']) ||
                in_array($value->menubar_page_name, ['Privacy Policy'])
            ) {
                $keyArray[] = $value->menubar_page_name;
            }
        }
    }

?>
<?php $__env->startSection('language-bar'); ?>
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text">
                    
                    <?php echo e(ucfirst($LangName->fullName)); ?>

                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                <?php $__currentLoopData = Utility::languages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('register', [$refId, $code])); ?>" tabindex="0"
                        class="dropdown-item <?php echo e($code == $lang ? 'active' : ''); ?>">
                        <span><?php echo e(ucFirst($language)); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </li>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card-body">
        <div class="">
            <h2 class="mb-3 f-w-600"><?php echo e(__('Register')); ?></h2>
        </div>
        <?php echo e(Form::open(['route' => ['register', 'plan' => $plan], 'method' => 'post', 'id' => 'loginForm', 'class' => 'needs-validation', 'novalidate'])); ?>


        <?php if(session('status')): ?>
            <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                <?php echo e(__('Email SMTP settings does not configured so please contact to your site admin.')); ?>

            </div>
        <?php endif; ?>
        <div class="custom-login-form">
            <div class="form-group mb-3">
                <label class="form-label"><?php echo e(__('Name')); ?></label>
                <?php echo e(Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Name'),'required'=>'required'])); ?>

                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="invalid-name text-danger" role="alert">
                        <strong><?php echo e($message); ?></strong>
                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="form-group mb-3">
                <label class="form-label"><?php echo e(__('Email')); ?></label>
                <?php echo e(Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Email'),'required'=>'required'])); ?>

                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="invalid-email text-danger" role="alert">
                        <strong><?php echo e($message); ?></strong>
                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="form-group mb-3">
                <label class="form-label"><?php echo e(__('Password')); ?></label>
                <?php echo e(Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Your Password'),'required'=>'required'])); ?>

                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="invalid-password text-danger" role="alert">
                        <strong><?php echo e($message); ?></strong>
                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="form-group mb-3">
                <label class="form-label"><?php echo e(__('Confirm Password')); ?></label>
                <?php echo e(Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => __('Enter Your Confirm Password'),'required'=>'required'])); ?>

                <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="invalid-password_confirmation text-danger" role="alert">
                        <strong><?php echo e($message); ?></strong>
                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <?php if(isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'yes'): ?>
                <?php if(isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2'): ?>
                    <div class="form-group mb-4">
                        <?php echo NoCaptcha::display($settings['cust_darklayout'] == 'on' ? ['data-theme' => 'dark'] : []); ?>

                        <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error small text-danger" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                <?php else: ?>
                    <div class="form-group col-lg-12 col-md-12 mt-3">
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" class="form-control">
                        <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="error small text-danger" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(count($keyArray) > 0): ?>
                <div class="form-check custom-checkbox">
                    <input type="checkbox" class="form-check-input <?php $__errorArgs = ['terms_condition_check'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        id="termsCheckbox" name="terms_condition_check">
                    <input type="hidden" name="terms_condition" id="terms_condition" value="off">

                    <label class="text-sm" for="terms_condition_check"><?php echo e(__('I agree to the ')); ?>

                        <?php $__currentLoopData = json_decode($landingPageSettings['menubar_page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(in_array($value->menubar_page_name, ['Terms and Conditions']) && isset($value->template_name)): ?>
                                <a href="<?php echo e($value->template_name == 'page_content' ? route('custom.page', $value->page_slug) : $value->page_url); ?>"
                                    target="_blank"><?php echo e($value->menubar_page_name); ?></a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if(count($keyArray) == 2): ?>
                            <?php echo e(__('and the ')); ?>

                        <?php endif; ?>
                        <?php $__currentLoopData = json_decode($landingPageSettings['menubar_page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(in_array($value->menubar_page_name, ['Privacy Policy']) && isset($value->template_name)): ?>
                                <a href="<?php echo e($value->template_name == 'page_content' ? route('custom.page', $value->page_slug) : $value->page_url); ?>"
                                    target="_blank"><?php echo e($value->menubar_page_name); ?></a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </label>
                </div>
                <?php $__errorArgs = ['terms_condition_check'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="error invalid-terms_condition_check text-danger" role="alert">
                        <strong><?php echo e(__('Please check this box if you want to proceed.')); ?></strong>
                    </span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php endif; ?>
            <input type="hidden" class="form-control" name="ref_id" value="<?php echo e($refId); ?>">

            <div class="d-grid">
                <?php echo e(Form::submit(__('Register'), ['class' => 'btn btn-primary btn-block mt-2', 'id' => 'saveBtn'])); ?>

            </div>
            <p class="my-4 text-center"><?php echo e(__('Already have an account?')); ?> <a href="<?php echo e(route('login')); ?>"
                    class="my-4 text-center text-primary"> <?php echo e(__('Login')); ?></a></p>
        </div>
        <?php echo e(Form::close()); ?>

    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('custom-scripts'); ?>
    
    <?php if(count($keyArray) > 0): ?>
        <script>
            $('#loginForm').on('submit', function() {
                if ($('#termsCheckbox').prop('checked')) {
                    $('#terms_condition').val('on');
                }
            });
        </script>
        <?php if(isset($settings['recaptcha_module']) && $settings['recaptcha_module'] == 'yes'): ?>
            <?php if(isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2'): ?>
                <?php echo NoCaptcha::renderJs(); ?>

            <?php else: ?>
                <script src="https://www.google.com/recaptcha/api.js?render=<?php echo e($settings['google_recaptcha_key']); ?>"></script>
                <script>
                    $(document).ready(function() {
                        grecaptcha.ready(function() {
                            grecaptcha.execute('<?php echo e($settings['google_recaptcha_key']); ?>', {
                                action: 'submit'
                            }).then(function(token) {
                                $('#g-recaptcha-response').val(token);
                            });
                        });
                    });
                </script>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/auth/register.blade.php ENDPATH**/ ?>