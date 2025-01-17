@php
    use App\ManageHarga;
@endphp
<form action="{{ route('manageharga.store') }}" method="POST">
    @csrf
    <input type="hidden" name="month" id="month" value="{{ $bulan }}"/>
    <input type="hidden" name="year" id="year" value="{{ $tahun }}"/>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Index Product</h4>

                <table id="responsive-datatable" class="table table-bordered dt-responsive wrap" cellspacing="0">
                    <thead>
                        <th style="width:5%">No</th>
                        <th style="width:5%">Product ID</th>
                        {{-- <th width="col-md-3">Prod ID Baru</th> --}}
                        <th width="10%">Product Name</th>
                        <th style="width:10%">Product Brand</th>
                        <th style="width:20%">Harga Distributor</th>
                        <th style="width:20%">Harga Modal</th>
                        <th style="width:10%">Posting Bulan Ini</th>
                        <th style="width:10%">Selisih</th>
                        <th style="width:10%">Bonus</th>
                    </thead>

                    <tbody>
                        @foreach($prods as $prd)
                        <tr>
                            @php
                                $i++;
                            @endphp
                            <td>{{$i}}</td>
                            <input type="hidden" name="i" id="i" value="{{ $i }}"/>
                            <td>{{$prd->prod_id}}</td>
                            <input type="hidden" name="pid[]" id="pid{{ $i }}" value="{{ $prd->prod_id }}"/>
                            {{-- <td>{{$prd->prod_id_new}}</td> --}}
                            <td>{{$prd->name}}</td>
                            <td>{{$prd->category}}</td>
                                @php
                                    $data1 = ManageHarga::where('prod_id', $prd->prod_id)->where('month', $bulan)->where('year', $tahun)->select('harga_distributor','harga_modal')->first();

                                    if(($data1['harga_distributor'] == "") OR ($data1['harga_distributor'] == null) OR ($data1['harga_modal'] == "") OR ($data1['harga_modal'] == null)){
                                        $harga_dist = 0;
                                        $harga_mod = 0;
                                    }else{
                                        $harga_dist = $data1['harga_distributor'];
                                        $harga_mod = $data1['harga_modal'];
                                    }

                                @endphp
                            <td>
                                <input class="form-control" name="price_dis[]" type="text" value="{{ $harga_dist }}">
                            </td>
                            <td>
                                <input class="form-control" name="price_mod[]" type="text" value="{{ $harga_mod }}">
                            </td>
                            @php
                                $postingan = DB::table('tblpotrxdet')->where('prod_id', $prd->prod_id)->join('tblpotrx','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrx.month',$bulan)->where('tblpotrx.year',$tahun)->sum('tblpotrxdet.qty');
                                $selisih = $harga_dist - $harga_mod;
                                $bonus = $postingan * $selisih;
                            @endphp
                            <td><span class="divide">{{ $postingan }}</span></td>
                            <td><span class="divide">{{ $selisih }}</span></td>
                            <td><span class="divide">{{ $bonus }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="form-group text-right m-b-0">
                    <button class="btn btn-primary waves-effect waves-light">
                        Update Harga
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
    <script type="text/javascript">
        $(document).ready(function () {
            // Responsive Datatable
            $('#responsive-datatable').DataTable({
                paging : false,
                scrollY: 400
            });
        });
        function btnSave(){
            var mo = $('#month').val();
            var ye = $('#year').val();
            console.log(ye)

        }
    </script>
