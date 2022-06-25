<div class="p-6 sm:px-20 bg-white border-b border-gray-200">

    <div class="mt-6 text-gray-500">

        <table id="data" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Datetime</th>
                    <th>Error</th>
                    <th>File</th>
                    <th>Line</th>
                </tr>
            </thead>
        </table>

        <br><hr>
        <b>ENDPOINT</b> {{ config('app.url') }}/data<br>
        <b>APIKEY (prod)</b> {{ config('app.apikeyprod') }}<br>
        <b>APIKEY (dev)</b> {{ config('app.apikeydev') }}

        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#data').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "ajax": "/data",
                    "order": [[ 1, "desc" ]],
                    "columns": [
                        { "data": "code" },
                        { "data": "datetime" },
                        { "data": "error" },
                        { "data": "file" },
                        { "data": "line" },
                    ],
                    "columnDefs": [
                        {
                            "targets": 0,
                            "render": function (data, type, row, meta) {
                                return '<a target="_blank" href="/data/'+data+'"><b>'+data+'</b></a>';
                            }
                        },
                        {
                            "targets": 2,
                            "render": function (data, type, row, meta) {
                                return '<div style="max-width: 275px; word-wrap: break-word;">'+data+'</div>';
                            }
                        },
                        {
                            "targets": 3,
                            "render": function (data, type, row, meta) {
                                return '...'+data.slice(-24);
                            }
                        }
                    ]
                } );
            } );
        </script>

    </div>
</div>

