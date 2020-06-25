<div class="card-box table-responsive">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#graph" data-toggle="tab" aria-expanded="false" class="nav-link active">
                Grafik
            </a>
        </li>
        <li class="nav-item">
            <a href="#table" data-toggle="tab" aria-expanded="true" class="nav-link">
                Tabel
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="graph">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        @if ($start <> NULL && $end <> NULL)
                            <h4 class="header-title m-t-0 m-b-30">Grafik Laporan Penjualan pada  {{ $start }} hingga {{ $end }}</h4>
                        @else
                            <h4 class="header-title m-t-0 m-b-30">Grafik Laporan Penjualan</h4>
                        @endif
                        <input type="hidden" name="count" id="count" value="{{ $count }}">
                        <div class="text-center">
                            <ul class="list-inline chart-detail-list">
                                @foreach($report as $rep)
                                    <input type="hidden" name="customer[]" value="{{ $rep['customer'] }}">
                                    <input type="hidden" name="bv[]" value="{{ $rep['bv'] }}">
                                    <input type="hidden" name="price[]" value="{{ $rep['price'] }}">
                                @endforeach

                                <li class="list-inline-item">
                                    <h5><i class="fa fa-circle m-r-5" style="color: #ff8acc;"></i>BV</h5>
                                </li>
                                <li class="list-inline-item">
                                    <h5 style="color: #5b69bc;"><i class="fa fa-circle m-r-5"></i>Omset</h5>
                                </li>
                            </ul>
                        </div>
                        <div id="morris-bar-stacked" class="morris-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="table">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        @if ($start <> NULL && $end <> NULL)
                            <h4 class="header-title m-t-0 m-b-30">Tabel Laporan Penjualan pada  {{ $start }} hingga {{ $end }}</h4>
                        @else
                            <h4 class="header-title m-t-0 m-b-30">Tabel Laporan Penjualan</h4>
                        @endif
                        <table id="datatable" class="table table-bordered">
                            <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Customer</th>
                                <th>BV</th>
                                <th>Omset</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($i=1)
                            @foreach ($report as $rep2)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $rep2['customer'] }}</td>
                                    <td>{{ $rep2['bv'] }}</td>
                                    <td>{{ $rep2['price'] }}</td>
                                </tr>
                                @php($i++)
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        $(".divide").divide();
        $('#datatable').DataTable({
            "columns": [
                { "data": "no"},
                { "data": "customer"},
                { "data": "bv", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
                { "data": "price", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' ) },
            ],
        });
    });
    $("#morris-bar-stacked").css("height","700");
    var count = $("#count").val();
    var customer = $("input[name='customer[]']").map(function(){return $(this).val();}).get();
    var bv = $("input[name='bv[]']").map(function(){return $(this).val();}).get();
    var price = $("input[name='price[]']").map(function(){return $(this).val();}).get();
    var array = [];
    var data;

    for(i=0; i<count; i++){
        cust = customer[i];
        bonus = bv[i];
        omset = price[i];

        var a = {y:cust, a:bonus, b:omset};
        array.push(a);
    }

    Morris.Bar({
        element: 'morris-bar-stacked',
        data: array,
        xkey: 'y',
        ykeys: ['a', 'b'],
        stacked: false,
        labels: ['BV', 'Omset'],
        hideHover: 'auto',
        resize: false, //defaulted to true
        gridLineColor: '#5b69bc',
        horizontal:true,
        barColors: ['#ff8acc', '#5b69bc']
    });
</script>
