<div id="employee_barcode" class="container">
    <div class="row">
        <div class="col-12">
            <div id="barcodeTarget"
                 style="position:relative;font-weight:bold;margin:0.5em auto 0;width:330px;height:200px"></div>
            <script>
                var card = '<?php echo $data['content']['card_no'] ?>';
                var btype = 'code128'; // code11, code39, code93, code128, codabar
                var renderer = 'css'; // css, bmp
            </script>
        </div>
    </div>
</div>
