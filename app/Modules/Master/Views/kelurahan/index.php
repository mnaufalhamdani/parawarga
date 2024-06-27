<?= $this->extend('layouts/template'); ?>

<?= $this->section('content'); ?>

<div class="col-md-12 order-1">
    <div class="card">
        <div class="table-responsive mx-4 my-4">
            <table class="table" id="datatables">
                <thead>
                    <tr>
                        <td>Kelurahan</td>
                        <td>Kecamatan</td>
                        <td>Kab / Kota</td>
                        <td>Provinsi</td>
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
            ajax:{
                url: '<?= base_url('master/data_kelurahan') ?>'
            },
            columns: [
                { data: "kelurahan_name" },
                { data: "kecamatan_name" },
                { data: "kabupaten_name" },
                { data: "provinsi_name" },
            ]
        });
    });
</script>

<?= $this->endSection(); ?>