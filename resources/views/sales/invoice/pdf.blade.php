@if ($jenis == "print")
    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
@endif
<div class="row" id="print-area">
    <div class="col-md-12">
        <div class="card-box">
            <!-- <div class="panel-heading">
                <h4>Invoice</h4>
            </div> -->
            <div class="panel-body">
                <div class="clearfix">
                    <div class="pull-left">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="">
                    </div>
                    <div class="pull-right">
                        <h4>Sales Order Invoice # <br>
                        </h4>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">

                        <div class="pull-left m-t-30">
                            <address>
                                <strong>ROYAL WAREHOUSE HERBAL STORE</strong><br>
                                {{$transaksi->customer->apadd}}<br>
                                Telepon: {{$transaksi->customer->apphone}}
                            </address>
                        </div>
                        <div class="pull-right m-t-30">
                            <p><strong>Customer Name: </strong> {{$transaksi->customer->apname}}</p>
                            <p><strong>Transaction Date: </strong> {{$transaksi->trx_date}}</p>
                            <p class="m-t-10"><strong>Transaction ID: </strong> #{{$transaksi->id}}</p>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="m-h-50"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
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
                                    <tr>
                                      <td>{{$i}}</td>
                                      <td>{{$item->product->name}}</td>
                                      <td>{{$item->qty}}</td>
                                      <td>{{$item->unit}}</td>
                                      <td>Rp. {{number_format($item->price)}}</td>
                                      <td>Rp. {{number_format($item->sub_ttl)}}</td>
                                    </tr>
                                  @php($i++)
                                  @endforeach
                                </tbody>
                            </table>
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-6">
                        <div class="clearfix m-t-40">
                            <div class="col-6">
                                <h5 class="small text-inverse font-600">Nama Pengirim:</h5>
                                <h5 class="small text-inverse font-600">Tanda Tangan:</h5><br><br>
                            </div>
                            <div class="col-6">
                                <h5 class="small text-inverse font-600">Nama Penerima:</h5>
                                <h5 class="small text-inverse font-600">Tanda Tangan:</h5><br><br>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-6 offset-xl-3">
                        <p class="text-right"><b>Sub-total:</b> Rp. {{number_format($transaksi->ttl_harga)}}</p>
                        <p class="text-right">Ongkir: Rp. {{number_format($transaksi->ongkir)}}</p>
                        <hr>
                        <h3 class="text-right">Rp. {{number_format($transaksi->ongkir+$transaksi->ttl_harga)}}</h3>
                    </div>
                </div>
                <hr>
                <div class="d-print-none">
                    <div class="pull-right">
                        <a href="#" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5" onclick="printPdf()"><i class="fa fa-print"></i>Print PDF</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
  function printPdf(){
    url = "{{route('invoicePrint',['jenis' => 'Print','trx_id'=>22])}}";
    windowUrl = url.replace("&amp", "");
    windowName = "Invoice";
    var printWindow = window.open(windowUrl, windowName, 'left=50000,top=50000,width=0,height=0');
    printWindow.focus();
    printWindow.print();
  }
    
</script>