@extends('layout.main')

@section('css')
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
    Product
@endsection

@section('content')
<!--main content start-->
<section id="main-content">
  <section class="wrapper">
    {{--  <h3><i class="fa fa-angle-right"></i>Tabel Data Sales</h3>  --}}
    <div class="row">
      <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Mutasi Produk</h4>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nama" class="col-form-label">Nama Produk</label>
                    <input type="text" class="form-control" name="nama" value="@isset($product->name){{ $product->name }}@endisset" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label for="product_code" class="col-form-label">Kode Produk</label>
                    <input type="text" class="form-control" name="product_code" value="@isset($product->prod_id){{ $product->prod_id }}@endisset" readonly>
                </div>
            </div>
            <hr>
            <table id="datatable" class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal Transaksi</th>
                    <th>Transaksi ID</th>
                    <th>Status</th>
                    <th>Jumlah</th>
                </tr>
                </thead>
                <tbody>
                @php
                    use App\Product;
                    $i=0;
                @endphp
                @foreach ($result as $r)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $r['tanggal'] }}</td>
                        <td>{{ $r['trx_id'] }}</td>
                        <td>{{ $r['status'] }}</td>
                        <td>{{ $r['qty'] }}</td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach
                </tbody>
            </table>
            <hr>
            <div class="form-group row">
                <label for="trx_id" class="col-4 col-form-label">Total Stock</label>
                <div class="col-8">
                    <div class="input-group">
                        <input type="text" class="form-control" name="total" id="total" value="{{ $total }}" readonly>
                    </div>
                </div>
            </div>
            <div class="form-group text-right m-b-0">
                <a href="/stockcontrolling"><button type="button" class="btn btn-warning waves-effect">Kembali</button></a>
            </div>
        </div>
      </div>
    </div>
  </section>
</section>

<!-- /MAIN CONTENT -->
<!--main content end-->
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>
@endsection

@section('script-js')
<script type="text/javascript">
    $(document).ready(function () {

        // Default Datatable
        $('#datatable').DataTable();
        $(".number").divide();

        //Buttons examples
        var table = $('#datatable-buttons').DataTable({
            lengthChange: false,
            buttons: ['copy', 'excel', 'pdf']
        });

        // Key Tables
        $('#key-table').DataTable({
            keys: true
        });

        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        // Multi Selection Datatable
        $('#selection-datatable').DataTable({
            select: {
                style: 'multi'
            }
        });

        table.buttons().container()
            .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
    });

</script>
@endsection
