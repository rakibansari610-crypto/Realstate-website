</div>
<!-- END layout-wrapper -->


<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ asset('assets/js/plugins.js') }}"></script>

<!-- apexcharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- Vector map-->
<script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

<!--Swiper slider js-->
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>


<!-- Load jQuery First -->
<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>

<!-- Then Load DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<!--datatable js-->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<!-- Dashboard init -->
<script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
<!-- App js -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>


<script src="{{ asset('assets/js/select2.min.js') }}"></script>
  {{-- <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>

$(document).ready(function() {
  $('#summernote').summernote({
    height: 300,
    popover: {
      image: [

        ['custom', ['examplePlugin']],
        ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
        ['float', ['floatLeft', 'floatRight', 'floatNone']],
        ['remove', ['removeMedia']]
      ]
    }
  });
  
});



    function DeleteItems(page, action, id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to delete this item?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6', // Blue button
            cancelButtonColor: '#d33', // Red button
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect on confirmation
                window.location.href = `${page}/${action}/${id}`;
            }
        });
    }

    function renderAjaxError($target, xhr, url) {
        let msg = 'Unknown error';
        if (xhr.responseJSON) {
            if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
            if (xhr.responseJSON.errors) {
                const list = Object.values(xhr.responseJSON.errors).flat();
                msg += '<br>' + list.join('<br>');
            }
        } else if (xhr.responseText) {
            // fallback: first 500 chars of HTML/text
            msg = xhr.responseText.slice(0, 500);
        }
        $target.html(`
    <div class="alert alert-danger mb-0">
      <strong>Failed:</strong> ${xhr.status} ${xhr.statusText}<br>
      <small>${url}</small><br>
      ${msg}
    </div>
  `);
        console.error('AJAX error', {
            url,
            status: xhr.status,
            statusText: xhr.statusText,
            xhr
        });
    }
    // function DeleteItems(page, action, id) {
    //     var where_to = confirm("Do you really want to Delete this??");
    //     if (where_to == true) {
    //         window.location.href = page + "/" + action + "/" + id;
    //     }
    // }
</script>
<script>
    (function() {
        function autoGrow(el) {
            if (!el) return;
            el.style.height = '0px'; // reset first
            el.style.height = (el.scrollHeight) + 'px';
        }

        // Grow all current textareas on load
        document.querySelectorAll('textarea.item-details').forEach(autoGrow);

        // Delegate: any future textarea edits (and newly added rows) auto-grow
        const table = document.getElementById('invoice-items-table');
        (table || document).addEventListener('input', function(e) {
            if (e.target && e.target.matches('textarea.item-details')) autoGrow(e.target);
        });

        // If you programmatically insert rows with prefilled text, call:
        // autoGrow(newTextareaElement);
    })();



        function updateClock() {
            const clock = document.getElementById('live-clock');
            if (!clock) return;
            const now = new Date();
            const options = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            clock.textContent = now.toLocaleTimeString([], options);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    <script>
    $(document).ready(function() {

         var $toggler = $('[href="#sidebarProjects"]');
        var $icon = $toggler.find('.arrow-icon');
        var $target = $('#sidebarProjects');

        // Jab dropdown open ho
        $target.on('show.bs.collapse', function() {
            $icon.removeClass('la-angle-down').addClass('la-angle-up');
        });

        // Jab dropdown band ho
        $target.on('hide.bs.collapse', function() {
            $icon.removeClass('la-angle-up').addClass('la-angle-down');
        });




        $('.select2').select2({
            placeholder: "-- Select --",
            allowClear: true,
            width: '100%',
            dropdownCssClass: "black-select"
        });

        function toggleClearButton($wrapper) {
            const selected = $wrapper.find('.select2').val();
            const $button = $wrapper.find('.clearSelect');
            if (selected && selected.length > 0) {
                $button.show();
            } else {
                $button.hide();
            }
        }
        $('.select2').on('change', function() {
            const $wrapper = $(this).closest('.select2-wrapper');
            toggleClearButton($wrapper);
        });
        $('.clearSelect').on('click', function() {
            const $wrapper = $(this).closest('.select2-wrapper');
            const $select = $wrapper.find('.select2');
            $select.val(null).trigger('change');
            toggleClearButton($wrapper);
        });

        $('.select2-wrapper').each(function() {
            toggleClearButton($(this));
        });
    });
</script>
<script>
  $(document).ready(function() {
      $('.filter-select').select2({
          width: '100%',
          placeholder: "Select option",
          allowClear: true
      });
  });
</script>
@yield('scripts')
</body>

</html>
