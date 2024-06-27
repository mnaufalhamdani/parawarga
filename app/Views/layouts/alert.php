<?php 
    $alertCode = 'other';
    $alertMessage = 'Unknown message';
    if(session()->getFlashData('success_msg')) {
        $alertCode = 'success';
        $alertMessage = session()->getFlashData('success_msg');
    } else if(session()->getFlashData('info_msg')) {
        $alertCode = 'info';
        $alertMessage = session()->getFlashData('info_msg');
    } else if(session()->getFlashData('error_msg')) {
        $alertCode = 'danger';
        $alertMessage = session()->getFlashData('error_msg');
    }
?>

<?php if(session()->getFlashData()) : ?>
    <div class="bs-toast toast toast-placement-ex m-3 fade bg-<?= $alertCode ?> top-0 end-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bx bx-bell me-2"></i>
            <div class="me-auto fw-medium">Pemberitahuan</div>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <?= $alertMessage ?>
        </div>
    </div>
<?php endif; ?>