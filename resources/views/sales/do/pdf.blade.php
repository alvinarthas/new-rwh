<!DOCTYPE html>
<html>
<head>
    <title>Detail Gaji</title>
</head>
<body>
    <style type="text/css">
        th, td {
            border: 1px solid black;
            text-align: left;
            padding: 1px;
            font-size: 15px;
        }

        table{
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        h6 {
            display: block;
            font-size: .99em;
            margin-top: 1em;
            margin-bottom: 1em;
            margin-left: 0;
            margin-right: 0;
            font-weight: bold;
            text-align: center;
        }

        h5 {
            display: block;
            font-size: .80em;
            font-family: arial, sans-serif;
            margin-top: 1em;
            margin-bottom: 1em;
            margin-left: 0;
            margin-right: 0;
            font-weight: bold;
        }

        li {
            font-size: 10px;
        }
    </style>

    <div class="row" id="print-area">
        <div class="col-md-12">
            <h6><b>RWH HERBAL DELIVERY ORDER</b></h6>
            <hr><hr>
            <h5><b>TRX ID: DO-{{$do_id}}</b></h5>
            <h5><b>DATE: {{$date}}</b></h5>
            <h5><b>MARKETING: {{$customer}}</b></h5>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table m-t-30">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($data as $item)
                                        <tr>
                                            <td>{{$item['no']}}</td>
                                            <td>{{$item['product']}}</td>
                                            <td>{{$item['qty']}} {{$item['unit']}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <h5><b>Approve By</b></h5>
            <h5><b>Inventory Officer</b></h5>
        </div>
    </div>
</body>
</html>