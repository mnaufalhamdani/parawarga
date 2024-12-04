<?= $this->extend('layouts/template'); ?>

<?= $this->section('content'); ?>

<div class="col-md-12 order-1">
    <div class="card mb-4">
        <div class="card-body">
            <div class="text-end">
                <a href="<?= base_url($route.'/update/'.$model['id']) ?>">
                    <button type="button" class="btn btn-warning"><i class="bx bx-edit-alt me-1"></i> ubah</button>
                </a>

                <a href="javascript:void(0);">
                    <button type="button" class="btn btn-danger" onclick="doDelete(this.value)" value="<?= $model['id'] ?>" data-bs-toggle="modal" data-bs-target="#modalDelete"><i class="bx bx-trash me-1"></i> Hapus</button>
                </a>
            </div>
            <table class="table table-responsive">
                <tbody class="table-border-bottom-0">
                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Area</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['area_name'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Alamat</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['address'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Wilayah</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['kelurahan_name'] ?>, <?= $model['kecamatan_name'] ?>, <?= $model['kabupaten_name'] ?>, <?= $model['provinsi_name'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>No. Telp</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['phone'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Foto</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <div class="card col-md-2 col-sm-2">
                                <a href="<?= base_url('public/' . $model['photo_area']) ?>" target="_blank">
                                    <img src="<?= base_url('public/' . $model['photo_area']) ?>" class="card-image" alt="Card image" width="150px" height="150px">
                                </a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Created By</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['name'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Created At</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['created_at'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Updated At</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['updated_at'] ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>