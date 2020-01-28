<!DOCTYPE html>
<html>
<head>
    <title>Invoice Penjualan</title>
@if ($jenis == "print")
    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
@endif
</head>
<body>
<style type="text/css">
    th, td {
        border: 2px solid black;
        text-align: left;
        padding: 10px;
    }

    table{
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
</style>

<div class="row" id="print-area">
    <div class="col-md-12">
        <div class="card-box">
            <!-- <div class="panel-heading">
                <h4>Invoice</h4>
            </div> -->
            <div class="panel-body">
                <div class="clearfix">
                    <div class="pull-left">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="" width="200px" height="160px">
                    </div>
                    <center>
                        <br><br>
                        <h2><strong>ROYAL WAREHOUSE HERBAL STORE</strong></h2>
                    </center>
                </div>
                <hr>
                <center>
                    <h3><strong>- Sales Order Invoice -</strong></h3>
                </center>
                <div class="row">
                    <div class="col-md-12">

                        <div class="pull-left m-t-30">
                            <address>
                                <h3><strong>Customer Name: </strong> {{$transaksi->customer->apname}}</h3>
                                <h4>{{$transaksi->customer->apadd}}</h4>
                                <h4>Phone: {{$transaksi->customer->apphone}}</h4>
                            </address>
                        </div>
                        <div class="pull-right m-t-30">
                            <h3><strong>Transaction Date: </strong> {{ date('d M Y',strtotime($transaksi->trx_date))}}</h3>
                            <h3 class="m-t-10"><strong>Transaction ID: </strong> #{{$transaksi->id}}</h3>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="m-h-50"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <font size="3" face="Courier New" >
                            <table class="table m-t-30">
                                <thead>
                                <tr><th>#</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr></thead>
                                <tbody>
                                  @php($i=1)
                                  @foreach ($transaksidet as $item)
                                    @isset($item->product->name)
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{$item->product->name}}</td>
                                            <td>{{$item->qty}}</td>
                                            <td>{{$item->unit}}</td>
                                            <td>Rp {{number_format($item->price, 2, ",", ".")}}</td>
                                            <td>Rp {{number_format($item->sub_ttl, 2, ",", ".")}}</td>
                                        </tr>
                                        @php($i++)
                                    @endisset
                                  @endforeach
                                </tbody>
                            </table>
                            </font>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-6">
                        <div class="clearfix m-t-40">
                            <div class="col-12">
                                <h4>Nama Pengirim:</h4>
                                <h4 class="text-inverse font-600">Tanda Tangan:</h4><br><br>
                            </div>
                            <div class="col-6">
                                <h4 class="text-inverse font-600">Nama Penerima:</h4>
                                <h4 class="text-inverse font-600">Tanda Tangan:</h4><br><br>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-6">
                        <h3 class="text-right"><b>Sub-total:</b> Rp {{number_format($transaksi->ttl_harga, 2, ",", ".")}}</h3>
                        <h3 class="text-right">Delivery Fee: Rp {{number_format($transaksi->ongkir, 2, ",", ".")}}</h3>
                        <hr>
                        <h2 class="text-right">Rp. {{number_format($transaksi->ongkir+$transaksi->ttl_harga, 2, ",", ".")}}</h2>
                    </div>
                </div>
                <hr>
            </div>
        </div>

    </div>

</div>
</body>
</html>
