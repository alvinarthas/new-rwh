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
                            <h4 class="header-title m-t-0 m-b-30">Grafik Gross Profit pada  {{ $start }} hingga {{ $end }}</h4>
                        @else
                            <h4 class="header-title m-t-0 m-b-30">Grafik Gross Profit</h4>
                        @endif
                        <input type="hidden" name="count" id="count" value="{{ $count }}">
                        <div class="text-center">
                            <ul class="list-inline chart-detail-list">
                                @foreach($data as $item)
                                    <input type="hidden" name="cust[]" value="{{ $item['name'] }}">
                                    <input type="hidden" name="total[]" value="{{ $item['value'] }}">
                                @endforeach

                                <li class="list-inline-item">
                                    <h5><i class="fa fa-circle m-r-5" style="color: #ff8acc;"></i>Total</h5>
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
                            <h4 class="header-title m-t-0 m-b-30">Tabel Gross Profit pada  {{ $start }} hingga {{ $end }}</h4>
                        @else
                            <h4 class="header-title m-t-0 m-b-30">Tabel Gross Profit</h4>
                        @endif
                        <table id="datatable" class="table table-bordered">
                            <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                @if($jenis == 1)
                                <th>Product Name</th>
                                @else
                                <th>Customer</th>
                                @endif
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($i=1)
                            @foreach ($data as $key)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $key['name'] }}</td>
                                    <td>Rp <span class="divide">{{ number_format($key['value'],2,',','.') }}</span></td>
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
        $('#datatable').DataTable();
    });
    $("#morris-bar-stacked").css("height","700");
    var count = $("#count").val();
    var customer = $("input[name='cust[]']").map(function(){return $(this).val();}).get();
    var total = $("input[name='total[]']").map(function(){return $(this).val();}).get();
    var array = [];
    var data;

    for(i=0; i<count; i++){
        cust = customer[i];
        omset = total[i];
        
        if(omset > 0){
            var a = {y:cust, a:omset};
            array.push(a);
        }
    }

    Morris.Bar({
        element: 'morris-bar-stacked',
        data: array,
        xkey: 'y',
        ykeys: ['a'],
        stacked: false,
        labels: ['Total'],
        hideHover: 'auto',
        resize: false, //defaulted to true
        gridLineColor: '#5b69bc',
        horizontal:true,
        barColors: ['#ff8acc']
    });
</script>
