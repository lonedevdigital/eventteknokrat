<!-- jQuery -->
<script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}">
</script>
<!-- AdminLTE App -->
<script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/dist/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/dist/js/demo.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/dist/js/pages/dashboard.js') }}"></script> -->
<!-- Datatables -->
<script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}">
</script>
<script src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
</script>
<script
    src="{{ asset('TemplateAdmin/AdminLTE-3.1.0/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<!-- select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script> -->
<script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
    integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    $(function() {
        $('#dataTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
        });
    });
    //Get the button
    let mybutton = document.getElementById("btn-back-to-top");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {
        scrollFunction();
    };

    function scrollFunction() {
        if (
            document.body.scrollTop > 20 ||
            document.documentElement.scrollTop > 20
        ) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }
    // When the user clicks on the button, scroll to the top of the document
    mybutton.addEventListener("click", backToTop);

    function backToTop() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }

    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@stack('js')
</body>

</html>
