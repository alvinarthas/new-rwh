@php
    use App\ManageHarga;
@endphp
<form action="{{ route('manageharga.store') }}" method="POST">
    @csrf
    <input type="hidden" name="month" id="month" value="{{ $month }}"/>
    <input type="hidden" name="year" id="year" value="{{ $year }}"/>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Index Product</h4>

                <table id="responsive-datatable" class="table table-bordered dt-responsive nowrap" cellspacing="0">
                    <thead>
                        <th style="width:10%">No</th>
                        <th style="width:5%">Product ID</th>
                        {{-- <th width="col-md-3">Prod ID Baru</th> --}}
                        <th width="5%">Product Name</th>
                        <th style="width:10%">Product Brand</th>
                        <th style="width:10%">Posting Bulan Ini</th>
                        <th style="width:7%">Harga Distributor</th>
                        <th style="width:7%">Harga Modal</th>
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
                            <td>
                                @php
                                    $postingan = DB::table('tblpotrxdet')->where('prod_id', $prd->prod_id)->join('tblpotrx','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrx.month',$month)->where('tblpotrx.year',$year)->sum('tblpotrxdet.qty');
                                @endphp
                                {{ $postingan }}
                            </td>
                                @php
                                    $data1 = ManageHarga::where('prod_id', $prd->prod_id)->where('month', $month)->where('year', $year)->select('harga_distributor','harga_modal')->first();
                                    if($data1['harga_distributor']==0){
                                        if($month==1){
                                            $data1 = ManageHarga::where('prod_id', $prd->prod_id)->where('year', $year-1)->where('month', 12)->select('harga_distributor', 'harga_modal')->first();
                                        }
                                        else{
                                            $data1 = ManageHarga::where('prod_id', $prd->prod_id)->where('year', $year)->where('month', $month-1)->select('harga_distributor','harga_modal')->first();
                                            }
                                    }
                                @endphp
                            <td>
                                <input name="price_dis[]" type="text" id="price_dis{{ $i }}" value="{{ $data1['harga_distributor'] }}" size="15" maxlength="15"/>
                            </td>
                            <td>
                                <input name="price_mod[]" type="text" id="price_mod{{ $i }}" value="{{ $data1['harga_modal'] }}" size="15" maxlength="15"/>
                            </td>
                            <td>
                                @php($selisih=$data1['harga_distributor']-$data1['harga_modal'])
                                {{ $selisih }}
                            </td>
                            <td>
                                @php($bonus=$postingan*$selisih)
                                {{ $bonus }}
                            </td>
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
