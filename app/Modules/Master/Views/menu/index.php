<?= $this->extend('layouts/template'); ?>

<?= $this->section('content'); ?>

<div class="col-md-12 order-1">
    <div class="card">
        <div class="table-responsive mx-4 my-4">
            <div class="text-end">
                <a href="<?= base_url($route . '/create') ?>" class="btn btn-primary mb-4 mt-2">
                    <i class='bx bx-plus mx-1'></i>
                    Tambah Data
                </a>
            </div>

            <table class="table" id="datatables">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Level</td>
                        <td>Type</td>
                        <td width="10%">Action</td>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>

<script>
    $(document).ready(function () {
        $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            order: [[1, 'ASC']],
            language: {
                searchPlaceholder: 'Search Name'
            },
            ajax:{
                url: '<?= base_url('master/data_menu') ?>'
            },
            columns: [
                { data: "name" },
                { data: "level" },
                { data: "type_id" },
                { data: "action" },
            ]
        });
    });
</script>

<?= $this->endSection(); ?>