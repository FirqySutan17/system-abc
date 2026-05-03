  </div> <!-- End body-wrapper -->
</div> <!-- End page-wrapper -->


<script src="<?= base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js'); ?>"></script>

<script src="<?= base_url('assets/js/sidebarmenu.js'); ?>"></script>
<script src="<?= base_url('assets/js/app.min.js'); ?>"></script>
<!-- <script src="<?= base_url('assets/libs/apexcharts/dist/apexcharts.min.js'); ?>"></script> -->
<script src="<?= base_url('assets/libs/simplebar/dist/simplebar.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('assets/js/dashboard.js'); ?>"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
      const toggles = document.querySelectorAll(".custom-arrow-toggle");

      toggles.forEach(toggle => {
          toggle.addEventListener("click", function () {
              const arrow = this.querySelector(".custom-arrow");
              arrow.classList.toggle("rotate");
          });
      });
  });

  function formatRupiah(val) {
      if (val === null || val === undefined) return '0';

      // paksa jadi string dulu
      val = val.toString();

      return val
          .replace(/[^0-9]/g, '')
          .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

</script>
</body>
</html>
