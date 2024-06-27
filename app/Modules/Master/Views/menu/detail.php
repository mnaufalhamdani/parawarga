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
                            <label>Urutan</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['urutan'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Name</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['name'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Link</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['link'] ?> </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Icon</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['icon'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Level</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= ($model['level'] == 1) ? 'Menu' : 'Sub Menu' ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Parent</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['parent_name'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Type</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0">
                                <?php if($model['type_id'] == 1) : ?>
                                    <span class="badge bg-label-dark"> <?= $model['option_name'] ?> </span>
                                <?php endif; if($model['type_id'] == 2) : ?>
                                    <span class="badge bg-label-info"> <?= $model['option_name'] ?> </span>
                                <?php endif; ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="align-middle table-body-title">
                            <label>Keterangan</label>
                        </td>
                        <td width="10"><label>:</label></td>
                        <td class="py-3 table-body-content">
                            <p class="mb-0"><?= $model['keterangan'] ?></p>
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