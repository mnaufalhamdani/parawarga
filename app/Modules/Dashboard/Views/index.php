<?= $this->extend('layouts/template'); ?>

<?= $this->section('content'); ?>

<div class="col-md-12 order-1">
  <div class="row">
    <div class="col-lg-3 col-md-12 col-3 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded bg-label-success">
                <i class="bx bx-credit-card"></i>
              </span>
            </div>
          </div>
          <span class="fw-medium d-block mb-1">Profit</span>
          <h3 class="card-title mb-2">$12,628</h3>
          <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
        </div>
      </div>
    </div>
    
    <div class="col-lg-3 col-md-12 col-3 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded bg-label-info">
                <i class="bx bx-credit-card"></i>
              </span>
            </div>
          </div>
          <span class="fw-medium d-block mb-1">Profit</span>
          <h3 class="card-title mb-2">$12,628</h3>
          <small class="text-info fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-12 col-3 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded bg-label-primary">
                <i class="bx bx-credit-card"></i>
              </span>
            </div>
          </div>
          <span class="fw-medium d-block mb-1">Profit</span>
          <h3 class="card-title mb-2">$12,628</h3>
          <small class="text-primary fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-12 col-3 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="card-title d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
              <span class="avatar-initial rounded bg-label-warning">
                <i class="bx bx-credit-card"></i>
              </span>
            </div>
          </div>
          <span class="fw-medium d-block mb-1">Profit</span>
          <h3 class="card-title mb-2">$12,628</h3>
          <small class="text-warning fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-12 col-lg-12 order-2 order-md-3 order-lg-2 mb-4">
  <div class="card">
    <div class="row row-bordered g-0">
      <h5 class="card-header m-0 me-2 pb-3">Total Revenue</h5>
      <div id="totalRevenueChart" class="px-2"></div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>