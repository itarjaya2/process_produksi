 <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl">
                <div
                  class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column"
                >
                  <div class="mb-2 mb-md-0">
                        ©
                      
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                    PT. Arjaya Mukti Santosa
                  </div>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js  -->

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    <script src="{{ asset('') }}assets/js/main.js"></script>

    <!-- Page JS -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const currentUrl = window.location.href;
    
        document.querySelectorAll('#layout-menu .menu-link[href]').forEach(link => {
            if (link.href === currentUrl) {
                // aktifkan submenu
                const li = link.closest('.menu-item');
                li.classList.add('active');
    
                // buka parent menu
                const parentMenu = li.closest('.menu-sub')?.closest('.menu-item');
                if (parentMenu) parentMenu.classList.add('open');
            }
        });
    });
    </script>

  </body>
</html>

<!-- beautify ignore:end -->
