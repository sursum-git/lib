<?php
//__NM____NM__FUNCTION__NM__//
function sc_top_progressbar($color = "#29d")
{
    ?>
    sc_include_lib ("Jquery");
    <script src='https://unpkg.com/nprogress@0.2.0/nprogress.js'></script>
    <link rel='stylesheet' href='https://unpkg.com/nprogress@0.2.0/nprogress.css'/>

    <style>
        #nprogress .bar {
            background: <?php echo $color; ?> !important;
        }
    </style>

    <script>
        document.addEventListener('readystatechange', startLoading);

        function startLoading()
        {
            if (document.readyState == "loading" || document.readyState == "interactive") {
                NProgress.configure({ showSpinner: false });
                NProgress.start();
            } else {
                stopLoading();
            }
        }

        function stopLoading()
        {
            NProgress.done();
        }
    </script>
    <?php
}
?>