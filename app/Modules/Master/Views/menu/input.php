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
                    <label class="col-sm-2 col-form-label">Urutan</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control urutan" min="1" max="100" name="urutan" placeholder="Ketik sesuatu. . ." value="<?= @$model['urutan'] ?>"/>
                        <div class="invalid-feedback d-block error-urutan"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control name" name="name" placeholder="Ketik sesuatu. . ." value="<?= @$model['name'] ?>"/>
                        <div class="invalid-feedback d-block error-name"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Link</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control link" name="link" placeholder="Ketik sesuatu. . ." value="<?= @$model['link'] ?>"/>
                        <div class="invalid-feedback d-block error-link"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Icon</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control icon" name="icon" placeholder="Ketik sesuatu. . ." value="<?= @$model['icon'] ? @$model['icon'] : 'bx' ?>"/>
                        <div class="invalid-feedback d-block error-icon"></div>
                    </div>
                    <div class="col-sm-4">
                        <a href="https://boxicons.com/" class="btn btn-sm btn-outline-dark m-1" target="_blank">
                            Referensi: boxicon.com
                        </a>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Parent</label>
                    <div class="col-sm-6">
                        <select class="select2 form-control form-select parent_id" name="parent_id">
                            <option value="0" selected>Tanpa Parent</option>
                            <?php foreach ($parent as $key => $val): ?>
                                <option value="<?= $val['id'] ?>" <?= ($val['id'] == @$model['parent_id']) ? 'selected' : ''; ?>><?= $val['name'] ?></option>    
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback d-block error-parent_id"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Type</label>
                    <div class="col-sm-6">
                        <select class="select2 form-control form-select type_id" name="type_id">
                            <?php foreach ($type as $key => $val): ?>
                                <option value="<?= $val['id'] ?>" <?= ($val['id'] == @$model['type_id']) ? 'selected' : ''; ?>><?= $val['name'] ?></option>    
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback d-block error-type_id"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Keterangan</label>
                    <div class="col-sm-6">
                        <textarea type="text" class="form-control keterangan" name="keterangan" placeholder="Ketik sesuatu. . ." >
                            <?= @$model['keterangan'] ?>
                        </textarea>
                        <div class="invalid-feedback d-block error-keterangan"></div>
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