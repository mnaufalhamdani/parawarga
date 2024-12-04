<?= $this->extend('layouts/template'); ?>

<?= $this->section('content'); ?>

<?php
    $formName = (@$newRecord) ? '/onCreate' : '/onUpdate';
?>

<div class="col-md-12 order-1">
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= base_url($route . $formName) ?>" method="POST" class="form-input" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Area</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control area_name" name="area_name" placeholder="Ketik sesuatu. . ." value="<?= @$model['area_name'] ?>"/>
                        <div class="invalid-feedback d-block error-area_name"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Alamat</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control address" id="name" name="address" placeholder="Ketik sesuatu. . ." value="<?= @$model['address'] ?>"/>
                        <div class="invalid-feedback d-block error-address"></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Provinsi</label>
                    <div class="col-sm-6">
                        <select class="select2 form-control form-select provinsi_id" name="provinsi_id" onchange="loadKabupaten()">
                            <option value="">Pilih Provinsi</option>
                            <?php foreach ($provinsi as $key => $val) : ?>
                                <option value="<?= $val['id'] ?>" <?= ($val['id'] == @$model['provinsi_id']) ? 'selected' : ''; ?>><?= $val['provinsi_name'] ?></option>    
                            <?php endforeach ?>
                        </select>
                        <div class="invalid-feedback d-block error-provinsi_id"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Kabupaten / Kota</label>
                    <div class="col-sm-6">
                        <select class="select2 form-control form-select kabupaten_id" name="kabupaten_id" onchange="loadKecamatan()"></select>
                        <div class="invalid-feedback d-block error-kabupaten_id"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Kecamatan</label>
                    <div class="col-sm-6">
                        <select class="select2 form-control form-select kecamatan_id" name="kecamatan_id" onchange="loadKelurahan()"></select>
                        <div class="invalid-feedback d-block error-kecamatan_id"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Kelurahan</label>
                    <div class="col-sm-6">
                        <select class="select2 form-control form-select kelurahan_id" name="kelurahan_id"></select>
                        <div class="invalid-feedback d-block error-kelurahan_id"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">No. Telp</label>
                    <div class="col-sm-6">
                        <input type="number" class="form-control phone" name="phone" placeholder="Ketik sesuatu. . ." value="<?= @$model['phone'] ?>"/>
                        <div class="invalid-feedback d-block error-phone"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Foto</label>
                    <div class="col-sm-6">
                        <div class="custom-file">
                            <input type="hidden" name="photo_area_old" value="<?= @$model['photo_area'] ?>">
                            <input type="file" class="form-control photo_area" id="image" name="photo_area" onchange="previewImage()">
                            <div class="invalid-feedback d-block error-photo_area"></div>
                        </div>
                    </div>
                    <div class="card col-md-2 col-sm-2">
                        <img src="<?= @$model['photo_area'] ? @$model['photo_area'] : base_url() . 'public/assets/img/default/img_default.png' ?>" class="card-image img-preview" alt="Card image">
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
        if($('.provinsi_id option:selected').val()){
            loadKabupaten();   
        }
        
        $('.form-input').submit(function(e) {
            e.preventDefault();

            var form = new FormData($('.form-input')[0]);
            form.append('photo_area',$('.photo_area')[0].files[0]);
            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: form,
                dataType: "json",
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
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

    function loadKabupaten() {
        $.post('<?= base_url($route . '/get_data_kabupaten') ?>', {
            provinsi_id : $('.provinsi_id option:selected').val()
        },
        function(result){
            $('.kabupaten_id').html('');
            $('.kecamatan_id').html('');
            $('.kelurahan_id').html('');
            var options = '<option value="">Pilih Kota/Kabupaten</option>';
            $.each(result, function(index , value){
                var selected = '';
                if(value.id == '<?= @$model['kabupaten_id']?>') { 
                    selected = 'selected';
                }
                options += '<option value="'+ value.id +'" ' + selected + '>'+ value.kabupaten_name +'</option>';
            });

            $('.kabupaten_id').html(options);

            if($('.kabupaten_id option:selected').val()){
                loadKecamatan();   
            }
        },
        "json")
    }

    function loadKecamatan() {
        $.post('<?= base_url($route . '/get_data_kecamatan') ?>', {
            kabupaten_id : $('.kabupaten_id option:selected').val()
        },
        function(result){
            $('.kecamatan_id').html('');
            $('.kelurahan_id').html('');
            var options = '<option value="">Pilih Kecamatan</option>';
            $.each(result, function(index , value){
                var selected = '';
                if(value.id == '<?= @$model['kecamatan_id']?>') { 
                    selected = 'selected';
                }
                options += '<option value="'+ value.id +'" ' + selected + '>'+ value.kecamatan_name +'</option>';
            });

            $('.kecamatan_id').html(options);

            if($('.kecamatan_id option:selected').val()){
                loadKelurahan();   
            }
        },
        "json")
    }

    function loadKelurahan() {
        $.post('<?= base_url($route . '/get_data_kelurahan') ?>', {
            kecamatan_id : $('.kecamatan_id option:selected').val()
        },
        function(result){
            $('.kelurahan_id').html('');
            var options = '<option value="">Pilih Kelurahan</option>';
            $.each(result, function(index , value){
                var selected = '';
                if(value.id == '<?= @$model['kelurahan_id']?>') { 
                    selected = 'selected';
                }
                options += '<option value="'+ value.id +'" ' + selected + '>'+ value.kelurahan_name +'</option>';
            });

            $('.kelurahan_id').html(options);
        },
        "json")
    }
</script>

<?= $this->endSection(); ?>