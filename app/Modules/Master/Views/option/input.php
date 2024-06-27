<?= $this->extend('layouts/template'); ?>

<?= $this->section('content'); ?>

<?php
    $formName = (@$newRecord) ? '/onCreate' : '/onUpdate';
?>

<div class="col-md-12 order-1">
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= base_url($route . $formName) ?>" method="POST" class="form-input">
                <?= csrf_field() ?>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control name" name="name" placeholder="Ketik sesuatu. . ." value="<?= @$model['name'] ?>"/>
                        <div class="invalid-feedback d-block error-name"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Option Type</label>
                    <div class="col-sm-6">
                        <select class="select2 form-control form-select option_type_id" name="option_type_id">
                            <option value="">Pilih Salah Satu</option>
                            <?php foreach ($option_type as $key => $val): ?>
                                <option value="<?= $val['id'] ?>" <?= ($val['id'] == @$model['option_type_id']) ? 'selected' : ''; ?>><?= $val['name_type'] ?></option>    
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback d-block error-option_type_id"></div>
                    </div>
                </div>

                <div class="row justify-content-end">
                    <div class="col-sm-10">
                        <input type="hidden" class="form-control" name="id" value="<?= @$model['id'] ?>"/>
                        <button type="submit" class="btn btn-primary btn-save">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>

<script>
    $(document).ready(function() {
        $('.form-input').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.btn-save').html('Loading. . .');
                },
                complete: function() {
                    $('.btn-save').html('Submit');
                },
                success: function(response) {
                    if(response.error) {
                        response.error.forEach(element => {
                            $('.' + element.name +'').removeClass('is-invalid');
                            if(element.message){
                                $('.' + element.name +'').addClass('is-invalid');
                                $('.error-' + element.name + '').html(element.message);
                            }
                        });
                    }else if(response.success) {
                        window.location.href = response.success;
                    }
                }
            });
            return false;
        });
    });
</script>

<?= $this->endSection(); ?>