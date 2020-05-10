<script src="<?= BASEASSETS; ?>/js/plugins/toastr/toastr.min.js"></script>
<script>
    toastr.option = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    <?php
        if(Session::hasSession(ADD_SUCCESS)):
    ?>
    toastr.success("new category has been added successfully !","Added");
    <?php
        Session::unsetSession(ADD_SUCCESS);
        elseif(Session::hasSession(ADD_ERROR)):
    ?>
    toastr.error("Adding a category failed","Failed !");
    <?php
    Session::unsetSession(ADD_ERROR);
    elseif(Session::hasSession('csrf')):
    ?>
    toastr.error("Unauthorized access, token mismatch !","Token Error !");
    <?php
    Session::unsetSession('csrf');
    endif;
    ?>
</script>