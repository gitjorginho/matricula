<?php
header("Content-Type: text/html;  charset=ISO-8859-1", true);
?>
<html>
    <head>
        <!-- style Administração -->
        <link rel="stylesheet" href="../../css/dist/css/adminlte.min.css">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="../../css/plugins/daterangepicker/daterangepicker.css">
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="form-group">
            <div class="row">
                <div class="col-md-2 offset-md-1">
                    <label for="">Data</label>
                    <input class="form-control date " type="text" id="date_agendamento">
                </div>
            </div>
        </div>
        <!-- jQuery -->
        <script src="../../css/plugins/jquery/jquery.min.js"></script>
        <!-- daterangepicker -->
        <script src="../../css/plugins/moment/moment.min.js"></script>
        <!-- daterangepicker -->
        <script src="../../css/plugins/daterangepicker/daterangepicker.js"></script>

        <script>
            $('.date').daterangepicker({
                "locale": {
                    "format": "DD/MM/YYYY",
                    "separator": " - ",
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Cancelar",
                    "daysOfWeek": [
                        "Dom",
                        "Seg",
                        "Ter",
                        "Qua",
                        "Qui",
                        "Sex",
                        "Sab"
                    ],
                    "monthNames": [
                        "Janeiro",
                        "Fevereiro",
                        "Março",
                        "Abril",
                        "Maio",
                        "Junho",
                        "Julho",
                        "Agosto",
                        "Setembro",
                        "Outubro",
                        "Novembro",
                        "Dezembro"
                    ],
                    "firstDay": 1
                },
                singleDatePicker: true,
                //showDropdowns: true,
                minYear: 2019,
                maxYear: parseInt(moment().format('YYYY'), 10)
            });
        </script>
    </body>
</html>
