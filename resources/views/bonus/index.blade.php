@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--venobox lightbox-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/magnific-popup/dist/magnific-popup.css') }}"/>
    <!--select2-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--datepicker-->
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!--Token-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
@php
    use App\Coa;
    use App\Perusahaan;
@endphp
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">

                @if($bonusapa=="perhitungan")
                    <h3 class="m-t-0 header-title">Perhitungan Bonus Member</h3>
                    @if (array_search("BMPBC",$page))
                        @if($jenis == "index")
                            <p class="text-muted font-14 m-b-30">
                                <a href="{{ route('bonus.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Perhitungan Bonus</a>
                            </p>
                        @endif
                    @endif
                @elseif($bonusapa=="pembayaran")
                    <h3 class="m-t-0 header-title">Penerimaan Bonus Member</h3>
                @elseif($bonusapa=="topup")
                    <h3 class="m-t-0 header-title">Top Up Bonus Member</h3>
                @elseif($bonusapa=="laporan" OR $bonusapa=="bonusgagal" OR $bonusapa=="estimasi")
                    @if($bonusapa=="laporan" OR $bonusapa=="estimasi")
                        @if($bonusapa=="laporan")
                            <h3 class="m-t-0 header-title">Laporan Realisasi Bonus Member</h3>
                        @elseif($bonusapa=="estimasi")
                            <h3 class="m-t-0 header-title">Laporan Realisasi Estimasi & Perhitungan Bonus Member</h3>
                        @endif
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Bulan & Tahun Bonus</label>
                            <div class="col-5">
                                <select class="form-control select2" parsley-trigger="change" name="bulan" id="bulan" required>
                                    <option value="#" selected disabled>Pilih Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-5">
                                <select class="form-control select2" parsley-trigger="change" name="tahun" id="tahun" required>
                                    <option value="#" selected disabled>Pilih Tahun</option>
                                    @for ($i = 2018; $i <= date('Y'); $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    @elseif($bonusapa=="bonusgagal")
                        <h3 class="m-t-0 header-title">Laporan Upload Gagal Perhitungan Bonus Member</h3>
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>Tanggal Transaksi</th>
                                <th>Jenis Bonus</th>
                                <th>File</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                    $total_bonus = 0;
                                @endphp
                                @foreach($bonusgagal as $bg)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$bg->tgl}}</td>
                                    <td>{{$bg->jenis}}</td>
                                    <td><a href="{{ public_path('download/bonusgagal/'.$bg->file) }}">{{$bg->file}}</a></td>
                                    <td>
                                        @if (array_search("BMGUD",$page))
                                            <form class="" action="{{ route('bonus.deletegagalbonus', ['id' => $bg->id]) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif
                @if($jenis == "index")
                    @if($bonusapa=="pembayaran")
                        @if (array_search("BMBBC",$page))
                            <p class="text-muted font-14 m-b-30">
                                <a href="{{ route('bonus.createPenerimaan') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Penerimaan Bonus</a>
                            </p>
                        @endif
                    @elseif($bonusapa=="topup")
                        @if (array_search("BMTUC",$page))
                            <p class="text-muted font-14 m-b-30">
                                <a href="{{ route('bonus.createtopup') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Top Up Bonus Member</a>
                            </p>
                        @endif
                    @endif
                @endif
                @if($jenis == "edit")
                    @if($bonusapa == "perhitungan")
                        <form class="form-horizontal" role="form" action="{{ route('bonus.update', ['id' => $bn->id_bonus]) }}" enctype="multipart/form-data" method="POST" onsubmit="return checkNull();">
                    @elseif($bonusapa == "pembayaran")
                        <form class="form-horizontal" role="form" action="{{ route('bonus.updatePenerimaan', ['id' => $bn->id_bonus]) }}" enctype="multipart/form-data" method="POST" onsubmit="return checkNull();">
                    @elseif($bonusapa == "topup")
                        <form class="form-horizontal" role="form" action="{{ route('bonus.updatetopup', ['id' => $bn->id_bonus]) }}" enctype="multipart/form-data" method="POST" onsubmit="return checkNull();">
                    @endif
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                @endif
                @if($bonusapa != "laporan" OR $bonusapa != "bonusgagal")
                    @if($jenis == "create" OR $jenis == "edit")
                        @if($jenis=="edit")
                            @if($bonusapa=="perhitungan" OR $bonusapa=="pembayaran")
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">ID Jurnal</label>
                                    <div class="col-10">
                                        <input value="{{ $bonus[0]['id_jurnal'] }}" type="text" class="form-control" name="id_jurnal_lama" id="id_jurnal_lama" readonly>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Tanggal Transaksi</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" parsley-trigger="change" required placeholder="YYYY/MM/DD" name="tgl_transaksi" id="tgl_transaksi"  value="@isset($bn->tgl){{ $bn->tgl }}@endisset" data-date-format='yyyy-mm-dd' autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="ti-calendar"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                    @endif
                @endif
                @if($jenis=="index")
                    @if(($bonusapa=="perhitungan") OR ($bonusapa == "pembayaran") OR ($bonusapa == "topup"))
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            @if($bonusapa!="topup")
                                <th>ID Jurnal</th>
                            @endif
                            <th>Tanggal Transaksi</th>
                            @if($bonusapa=="perhitungan" OR $bonusapa=="pembayaran")
                                <th>Bulan</th>
                                <th>Tahun</th>
                            @endif
                            @if($bonusapa=="perhitungan")
                                <th>Perusahaan</th>
                            @endif
                            @if($bonusapa=="pembayaran")
                                <th>Rekening Bank Tujuan</th>
                            @elseif($bonusapa=="topup")
                                <th>Sumber Rekening</th>
                            @endif
                            <th>Total Bonus</th>
                            <th>Action</th>
                        </thead>

                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach($bonus as $b)
                            <tr>
                                <td>{{$i}}</td>
                                @if($bonusapa!="topup")
                                    <td><a href="javascript:;" onclick="getDetail('{{$b->id_jurnal}}', '{{$bonusapa}}')" class="btn btn-custom btn-trans waves-effect w-md waves-danger m-b-5">{{$b->id_jurnal}}</a></td>
                                @endif
                                <td>{{$b->tgl}}</td>
                                @if($bonusapa=="perhitungan" OR $bonusapa=="pembayaran")
                                    <td>{{$b->bulan}}</td>
                                    <td>{{$b->tahun}}</td>
                                @endif
                                @if($bonusapa=="perhitungan")
                                    <td>{{$b->nama}}</td>
                                @elseif($bonusapa=="pembayaran" OR $bonusapa=="topup")
                                    <td>{{$b->AccName}}</td>
                                @endif
                                <td>Rp {{ number_format($b->total_bonus, 2, ",", ".")}}</td>
                                <td>
                                    @if($bonusapa=="perhitungan")
                                        @if (array_search("BMPBU",$page))
                                            <a href="{{route('bonus.edit',['id'=>$b->id_bonus])}}" class="btn btn-info btn-rounded waves-effect waves-light w-md m-b-5">Update</a>
                                        @endif
                                        @if (array_search("BMPBD",$page))
                                            <form class="" action="{{ route('bonus.destroy', ['id' => $b->id_jurnal]) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button>
                                            </form>
                                        @endif
                                    @elseif($bonusapa=="pembayaran")
                                        @if (array_search("BMBBU",$page))
                                            <a href="{{route('bonus.editPenerimaan',['id'=>$b->id_bonus])}}" class="btn btn-info btn-rounded waves-effect waves-light w-md m-b-5">Update</a>
                                        @endif
                                        @if (array_search("BMBBD",$page))
                                            <form class="" action="{{ route('bonus.deletePenerimaan', ['id' => $b->id_jurnal]) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button>
                                            </form>
                                        @endif
                                    @elseif($bonusapa=="topup")
                                        @if (array_search("BMTUU",$page))
                                            <a href="{{route('bonus.edittopup',['id'=>$b->id_bonus])}}" class="btn btn-info btn-rounded waves-effect waves-light w-md m-b-5">Update</a>
                                        @endif
                                        {{-- @if (array_search("BMTUD",$page))
                                            <form class="" action="{{ route('bonus.deletetopup', ['id' => $b->tgl]) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button>
                                            </form>
                                        @endif --}}
                                    @endif
                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                @elseif($jenis == "create" OR $jenis == "edit")
                    <input type="hidden" name="bonusapa" id="bonusapa" value="{{ $bonusapa }}">

                    @if($bonusapa=="perhitungan" OR $bonusapa=="bonusgagal")
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Nama Perusahaan</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" id="perusahaan" name="perusahaan">
                                    @isset($bn->perusahaan_id)
                                        <option value="{{ $bn->perusahaan_id }}" selected>{{ Perusahaan::where('id', $bn->perusahaan_id)->first()->nama }}</option>
                                        @foreach ($perusahaans as $prs)
                                            @if($bn->perusahaan_id != $prs->id)
                                                <option value="{{$prs->id}}">{{$prs->nama}}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="#" disabled selected>Pilih Perusahaan</option>
                                        @foreach ($perusahaans as $prs)
                                            <option value="{{$prs->id}}">{{$prs->nama}}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        </div>
                    @endif

                    @if($bonusapa=="perhitungan" OR $bonusapa=="pembayaran")
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Bulan & Tahun Bonus</label>
                        <div class="col-5">
                            <select class="form-control select2" parsley-trigger="change" name="bulan" id="bulan" onchange="checkEstimasiBonus()" required>
                                @isset($bn->bulan)
                                    <option value="{{$bn->bulan}}" selected>{{date("F", mktime(0, 0, 0, $bn->bulan, 10))}}</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        @if($i != $bn->bulan)
                                            <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                        @endif
                                    @endfor
                                    <input type="hidden" name="bulan_lama" value="{{$bn->bulan}}">
                                @else
                                    <option value="#" selected disabled>Pilih Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                    @endfor
                                @endisset
                            </select>
                        </div>
                        <div class="col-5">
                            <select class="form-control select2" parsley-trigger="change" name="tahun" id="tahun" onchange="checkEstimasiBonus()" required>
                                @isset($bn->tahun)
                                    <option value="{{ $bn->tahun }}" selected>{{ $bn->tahun }}</option>
                                    @for ($i = 2018; $i <= date('Y'); $i++)
                                        @if($bn->tahun != $i)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endif
                                    @endfor
                                    <input type="hidden" name="tahun_lama" value="{{$bn->tahun}}">
                                @else
                                    <option value="#" selected disabled>Pilih Tahun</option>
                                    @for ($i = 2018; $i <= date('Y'); $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif
                    @if($bonusapa=="pembayaran" OR $bonusapa=="topup")
                        <div class="form-group row">
                            @if($bonusapa=="pembayaran")
                                <label class="col-2 col-form-label">Rekening Bank Tujuan</label>
                            @elseif($bonusapa=="topup")
                                <label class="col-2 col-form-label">Sumber Rekening</label>
                            @endif
                            <div class="col-10">
                                <select class="form-control" parsley-trigger="change" id="rekening" name="rekening">
                                    @isset($bn->AccNo)
                                        <option value="{{ $bn->AccNo }}" selected>{{ Coa::where('AccNo', $bn->AccNo)->first()->AccName }}</option>
                                        <input type="hidden" name="rekening_lama" value="{{$bn->AccNo}}">
                                    @endisset
                                </select>
                            </div>
                        </div>
                        @isset($bn->AccNo)
                            @if($bn->supplier != 0)
                                <div id="supplier_show" style="display:block">
                            @else
                                <div id="supplier_show" style="display:none">
                            @endif
                        @else
                            <div id="supplier_show" style="display:none">
                        @endisset
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Supplier</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" id="supplier" name="supplier">
                                        @isset($bn->supplier)
                                            @if($bn->supplier != 0)
                                                <option value="{{ $bn->supplier }}">{{ Perusahaan::where('id', $bn->supplier)->first()->nama }}</option>
                                            @else
                                                <option value="0" selected disabled>Pilih Supplier</option>
                                                @foreach($supplier as $s)
                                                    @if($bn->supplier != $s->id)
                                                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @else
                                            <option value="0" selected disabled>Pilih Supplier</option>
                                            @foreach($supplier as $s)
                                                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="coa" id="coa">
                    @endif
                    @if($jenis=="edit")
                        <div class="row">
                            <div class="col-12">
                                <div class="card-box table-responsive">
                                    <h4 class="m-t-0 header-title">Detail Bonus Member</h4>
                                    @if($bonusapa=="perhitungan")
                                        <table id="editable-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Tambah Daftar Member</label>
                                                <div class="col-10">
                                                    <select class="form-control select2" id="search2" name="search2" parsley-trigger="change" onchange="addRowPerhitungan(this.value)">
                                                    </select>
                                                </div>
                                            </div>
                                            <thead>
                                                <th>No</th>
                                                <th>KTP</th>
                                                <th>No ID</th>
                                                <th>Nama</th>
                                                <th>Bonus</th>
                                                <th>Action</th>
                                            </thead>

                                            <tbody id="table-body">
                                                @php
                                                    $i = 1;
                                                    $total_bonus = 0;
                                                @endphp
                                                @foreach($bonus as $b)
                                                <tr style="width:100%" id="trsd{{ $b->id_bonus }}" class="trow">
                                                    <td><input type="hidden" name="id_bonus[]" value="{{ $b->id_bonus }}">{{$i++}}</td>
                                                    <td>{{$b->ktp}}</td>
                                                    <td><input type="hidden" name="noid[]" value="{{ $b->noid }}">{{$b->noid}}</td>
                                                    <td>{{$b->nama}}</td>
                                                    <td>
                                                        {{-- tampil semua data member perusahaan --}}
                                                        <input class="form-control" value="{{ $b['bonus'] }}" type="text" name="bonus[]" parsley-trigger="keyup" onkeyup="checkTotal()">
                                                        <input value="{{ $b['bonus'] }}" type="hidden" name="bonus_lama[]">
                                                        {{-- hanya yang bonus !=0 --}}
                                                        {{-- <input class="form-control number" value="{{ number_format($prm['bonus'],0) }}" type="text" name="bonus{{ $i }}"> --}}
                                                    </td>
                                                    <td>
                                                        <a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteRowPerhitungan({{ $b['id_bonus']}})">Delete</a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {{-- <input value="{{ $bonus[0]['id_jurnal'] }}" type="hidden" name="id_jurnal_lama" id="id_jurnal_lama"> --}}
                                    @elseif($bonusapa=="pembayaran" OR $bonusapa=="topup")
                                        <table id="editable-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Tambah Daftar Member</label>
                                                <div class="col-10">
                                                    @if($bonusapa=="pembayaran")
                                                        <select class="form-control select2" id="search" name="search" parsley-trigger="change" onchange="addRowPenerimaan(this.value)">
                                                        </select>
                                                    @elseif($bonusapa=="topup")
                                                        <select class="form-control select2" id="search" name="search" parsley-trigger="change" onchange="addRowTopUp(this.value)">
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>
                                            <thead>
                                                <th>No</th>
                                                @if($bonusapa=="topup")
                                                    <th>ID Jurnal</th>
                                                @endif
                                                @if($bn->AccNo != "1.1.1.1.000003")
                                                    <th>Nama Bank</th>
                                                    <th>No Rekening</th>
                                                @endif
                                                <th>Nama</th>
                                                <th>Bonus</th>
                                                <th>Action</th>
                                            </thead>

                                            <tbody id="table-body">
                                                @php
                                                    $i = 1;
                                                    $total_bonus = 0;
                                                @endphp
                                                @foreach($bonus as $b)
                                                <tr style="width:100%" id="trtd{{ $b->id_bonus }}" class="trow">
                                                    <td><input type="hidden" name="id_bonus[]" value="{{ $b->id_bonus }}">{{$i}}</td>
                                                    @if($bonusapa=="topup")
                                                        <td><input type="hidden" name="id_jurnal[]" value="{{ $b->id_jurnal }}">{{$b->id_jurnal}}</td>
                                                    @endif
                                                    @if($bn->AccNo != "1.1.1.1.000003")
                                                        <td>{{$b->namabank}}</td>
                                                        <td><input type="hidden" name="norekening[]" value="{{ $b->no_rek }}">{{$b->no_rek}}</td>
                                                    @else
                                                        <input type="hidden" name="norekening[]" value="{{ $b->no_rek }}">
                                                    @endif
                                                    <td>{{$b->nama}}</td>
                                                    <td>
                                                        {{-- tampil semua data member perusahaan --}}
                                                        <input class="form-control" value="{{ $b['bonus'] }}" type="text" name="bonus[]" parsley-trigger="keyup" onkeyup="checkTotal()">
                                                        @if($bonusapa=="pembayaran")
                                                            <input value="{{ $b['bonus'] }}" type="hidden" name="bonus_lama[]">
                                                        @endif
                                                        {{-- hanya yang bonus !=0 --}}
                                                        {{-- <input class="form-control number" value="{{ number_format($prm['bonus'],0) }}" type="text" name="bonus{{ $i }}"> --}}
                                                    </td>
                                                    <td>
                                                        @if($bonusapa=="pembayaran")
                                                            <a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteRowPenerimaan({{ $b->id_bonus}})">Delete</a>
                                                        @elseif($bonusapa=="topup")
                                                            @if (array_search("BMTUD",$page))
                                                                <a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteRowTopup({{ $b->id_bonus}})">Delete</a>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php
                                                    $i++;
                                                @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {{-- <input value="{{ $bonus[0]['id_jurnal'] }}" type="hidden" name="id_jurnal_lama"> --}}
                                    @endif
                                    <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Total Bonus</label>
                                        <div class="col-10">
                                            <input type="text" class="form-control divider" min="0" parsley-trigger="change" required name="total_bonus" id="total_bonus" value="{{ $total_bonus }}" readonly="readonly">
                                        </div>
                                    </div>
                                    @if($bonusapa=="perhitungan")
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Estimasi Bonus</label>
                                            <div class="col-10">
                                                <input type="text" class="form-control divider" min="0" parsley-trigger="change" required name="estimasi_bonus" id="estimasi_bonus" value="{{ $estimasi_bonus }}" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Selisih (Laba/Rugi)</label>
                                            <div class="col-10">
                                                <input type="text" class="form-control divider" min="0" parsley-trigger="change" required name="selisih_bonus" id="selisih_bonus" value="0" readonly="readonly">
                                            </div>
                                        </div>
                                    @elseif($bonusapa=="pembayaran" OR $bonusapa=="topup")
                                        @if($bonusapa=="pembayaran")
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Piutang Bonus Tertahan</label>
                                                <div class="col-10">
                                                    <input type="text" class="form-control divider" min="0" parsley-trigger="change" required name="bonus_tertahan" id="bonus_tertahan" value="{{ $bonus_tertahan }}" readonly="readonly">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-2 col-form-label">Selisih (Laba/Rugi)</label>
                                                <div class="col-10">
                                                    <input type="text" class="form-control divider" min="0" parsley-trigger="change" required name="selisih_bonus" id="selisih_bonus" value="0" readonly="readonly">
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                @if($jenis=="edit")
                    <div class="form-group text-right m-b-0">
                        @if($bonusapa=="perhitungan")
                            <a href="{{ route('bonus.index') }}" class="btn btn-warning waves-effect waves-light">
                                Kembali
                            </a>
                        @elseif($bonusapa=="pembayaran")
                            <a href="{{ route('bonus.penerimaan') }}" class="btn btn-warning waves-effect waves-light">
                                Kembali
                            </a>
                        @elseif($bonusapa=="topup")
                            <a href="{{ route('bonus.topup') }}" class="btn btn-warning waves-effect waves-light">
                                Kembali
                            </a>
                        @endif
                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                            Simpan
                        </button>
                    </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <div class="form-group text-right m-b-0">
        @if($bonusapa=="perhitungan")
            @if($jenis=="create")
                <a href="{{ route('bonus.index') }}"><button class="btn btn-warning waves-effect waves-light" type="submit">
                    Kembali
                </button></a>
                <button class="btn btn-primary waves-effect waves-light" onclick="createBonusPerhitungan()" type="submit">
                    Create Bonus
                </button>
            @endif
        @elseif($bonusapa=="pembayaran")
            @if($jenis=="create")
                <a href="{{ route('bonus.penerimaan') }}"><button class="btn btn-warning waves-effect waves-light" type="submit">
                    Kembali
                </button></a>
                <button class="btn btn-primary waves-effect waves-light" onclick="createBonusPenerimaan()" type="submit">
                    Create Bonus
                </button>
            @endif
        @elseif($bonusapa=="topup")
            @if($jenis=="create")
                <a href="{{ route('bonus.topup') }}"><button class="btn btn-warning waves-effect waves-light" type="submit">
                    Kembali
                </button></a>
                <button class="btn btn-primary waves-effect waves-light" onclick="createBonusTopup()" type="submit">
                    Create Bonus
                </button>
            @endif
        @elseif($bonusapa=="laporan")
            @if($jenis=="index")
                <button class="btn btn-primary waves-effect waves-light" onclick="showLaporanBonus()" type="submit">
                    Show Bonus
                </button>
            @endif
        @elseif($bonusapa=="estimasi")
            @if($jenis=="index")
                <button class="btn btn-primary waves-effect waves-light" onclick="showEstimasiBonus()" type="submit">
                    Show Bonus
                </button>
            @endif
        @endif
    </div>
    <div id="tblBonus">

    </div> <!-- end row -->
    <!--  Modal content for the above example -->
    <div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" id="do-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Detail Deposit Pembelian</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
                </div>
                <div class="modal-body" id="modalView">
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- Modal-Effect -->
    <script src="{{ asset('assets/plugins/custombox/dist/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custombox/dist/legacy.min.js') }}"></script>

    <!-- Magnific popup -->
    <script type="text/javascript" src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- number-divider -->
    <script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>

    <!-- Datepicker -->
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('script-js')

<script type="text/javascript">

    $(document).ready(function () {
        var bonusapa = $("#bonusapa").val();
        if(bonusapa=="pembayaran" || bonusapa=="topup"){
            ajx_member()
            console.log("else");
        }else if(bonusapa=="perhitungan"){
            ajx_member2()
            console.log("perhitungan")
        }

        checkTotal();
        ajx_coa();
        jQuery('#tgl_transaksi').datepicker();
        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        $('.divider').divide();

        $('#editable-datatable').DataTable({
            searching  : false,
            paging  : false,
            scrollY : 400,
        });

        $('.image-popup').magnificPopup({
            type: 'image',
        });

        // Select2
        $("#bulan").select2({
            templateResult: formatState,
            templateSelection: formatState
        });
        $("#tahun").select2({
            templateResult: formatState,
            templateSelection: formatState
        });
        $("#perusahaan").select2({
            templateResult: formatState,
            templateSelection: formatState
        });
        $("#supplier").select2({
            templateResult: formatState,
            templateSelection: formatState
        });

        function formatState (opt) {
            if (!opt.id) {
                return opt.text.toUpperCase();
            }

            var optimage = $(opt.element).attr('data-image');
            if(!optimage){
            return opt.text.toUpperCase();
            } else {
                var $opt = $(
                '<span><img src="' + optimage + '" width="60px" /> ' + opt.text.toUpperCase() + '</span>'
                );
                return $opt;
            }
        };

        $('#rekening').on('change', function () {
            checkCoa();
        });

    });

    function ajx_member(){
        var bid = $("#bank_id").val()
        var AccNo = $("#rekening").val()
        console.log("bank id = "+bid)
        $("#search").select2({
            placeholder:'Masukan Kata Kunci',
            ajax:{
                url: "{{route('ajxBonusOrder')}}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return{
                        keyword:params.term,
                        bankid:bid,
                        AccNo:AccNo,
                    };
                },
                processResults:function(data){
                    var item = $.map(data, (value)=>{ //map buat ngemap object data kyk foreach
                        if(AccNo == "1.1.1.1.000003"){
                            var text = value.nama+" (KTP : "+value.ktp+")";
                        }else{
                            var text = value.namabank+" "+value.norek+" - "+value.nama+" (KTP : "+value.ktp+")";
                        }
                        return { id: value.id, text: text};
                    });
                    return {
                        results: item
                    }
                },
                cache: false,
            },
            minimumInputLength: 3,
            // templateResult: formatRepo,
            // templateSelection: formatRepoSelection
        });

    }

    function ajx_member2(){
        var pid = $("#perusahaan").val()
        $("#search2").select2({
            placeholder:'Masukan Kata Kunci',
            ajax:{
                url: "{{route('ajxBonusOrderPerhitungan')}}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return{
                        keyword:params.term,
                        perusahaanid:pid,
                    };
                },
                processResults:function(data){
                    var item = $.map(data, (value)=>{ //map buat ngemap object data kyk foreach
                        return { id: value.id, text: value.noid+" - "+value.nama+" (KTP : "+value.ktp+")"};
                    });
                    return {
                        results: item
                    }
                },
                cache: false,
            },
            minimumInputLength: 3,
        });
    }

    function addRowPerhitungan(id){
        var token = $("meta[name='csrf-token']").attr("content");
        var prs = $("#perusahaan").val();
        var thn = $("#tahun").val();
        var bln = $("#bulan").val();
        var cnt = $("#ctr").val();
        $.ajax({
            url : "{{route('ajxAddRowPerhitungan')}}",
            type : "post",
            dataType: 'json',
            data:{
                id : id,
                tahun : thn,
                bulan : bln,
                count : cnt,
                _token : token,
            },
        }).done(function (data) {
            $('#table-body').append(data.append);
            // var cnt = parseInt($('#ctr').val()) + 1;
            var cnt = $('#editable-datatable tbody tr.trow').length;
            $('#ctr').val(cnt);
            resetall();
            checkTotal();
            // changeTotalHarga(data.sub_ttl);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function addRowPenerimaan(id){
        var token = $("meta[name='csrf-token']").attr("content");
        var thn = $("#tahun").val();
        var bln = $("#bulan").val();
        var cnt = $("#ctr").val();
        var AccNo = $("#rekening").val();
        $.ajax({
            url : "{{route('ajxAddRowPenerimaan')}}",
            type : "post",
            dataType: 'json',
            data:{
                id_member : id,
                tahun : thn,
                bulan : bln,
                count : cnt,
                _token : token,
                AccNo : AccNo,
            },
        }).done(function (data) {
            $('#table-body').append(data.append);
            var cnt = $('#editable-datatable tbody tr.trow').length;
            $('#ctr').val(cnt);
            resetall();
            checkTotal();
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function addRowTopUp(id){
        var token = $("meta[name='csrf-token']").attr("content");
        var tanggal = $("#tgl").val();
        var cnt = $("#ctr").val();
        $.ajax({
            url : "{{route('ajxAddRowTopup')}}",
            type : "post",
            dataType: 'json',
            data:{
                id_member : id,
                tgl : tanggal,
                jenis : "edit",
                count : cnt,
                _token : token,
            },
        }).done(function (data) {
            $('#table-body').append(data.append);
            var cnt = $('#editable-datatable tbody tr.trow').length;
            $('#ctr').val(cnt);
            resetall();
            checkTotal();
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function resetall(){
        var bonusapa = $("#bonusapa").val();
        if(bonusapa=="perhitungan"){
            $('#search2').empty().trigger('click')
        }else{
            $('#search').empty().trigger('click')
        }
    }

    function deleteItem(id){
        count = parseInt($('#ctr').val());
        $('#trow'+id).remove();
        console.log('delete');
        $('#ctr').val(count);
        checkTotal();
    }

    function deleteRowPerhitungan(id){
        var token = $("meta[name='csrf-token']").attr("content");
        console.log(id);
        $.ajax({
            url : "/perhitunganbonus/"+id+"/deleterow",
            type : "GET",
            dataType : "json",
            data:{
                id : id,
                _token : token,
            },success		:	function(data){
                $('#trsd'+id).remove();
                var cnt = $('#editable-datatable tbody tr.trow').length;
                $('#ctr').val(cnt);
                checkTotal();
            },
            error       :   function(data){
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            }
        })
    }

    function deleteRowPenerimaan(id){
        var token = $("meta[name='csrf-token']").attr("content");
        console.log(id);
        $.ajax({
            url : "/penerimaanbonus/"+id+"/deleterow",
            type : "GET",
            dataType : "json",
            data :{
                id : id,
                _token : token,
            },
            success	:	function(data){
                $('#trtd'+id).remove();
                var cnt = $('#editable-datatable tbody tr.trow').length;
                $('#ctr').val(cnt);
                checkTotal();
            },
            error       :   function(data){
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            }
        })
    }

    function deleteRowTopup(id){
        var token = $("meta[name='csrf-token']").attr("content");
        console.log(id);
        $.ajax({
            url : "/topupbonus/"+id+"/deleterow",
            type : "GET",
            dataType : "json",
            data:{
                id : id,
                _token : token,
            },success		:	function(data){
                $('#trtd'+id).remove();
                var cnt = $('#editable-datatable tbody tr.trow').length;
                $('#ctr').val(cnt);
                checkTotal();
            },
            error       :   function(data){
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            }
        })
    }

    function checkCoa(){
        id = $('#rekening').val();
        console.log(id)
        if(id == '1.1.3.3'){
            document.getElementById("supplier_show").style.display = 'block';
        }else{
            document.getElementById("supplier_show").style.display = 'none';
            $('#supplier').val(0);
        }
        $('#coa').val(id);
    }

    function ajx_coa(){
        $("#rekening").select2({
            placeholder:'Masukan Kata Kunci',
            ajax:{
                url: "{{route('ajxCoaOrder')}}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return{
                        keyword:params.term,
                    };
                },
                processResults:function(data){
                    var item = $.map(data, (value)=>{ //map buat ngemap object data kyk foreach
                        return { id: value.id, text: value.AccName};
                    });
                    return {
                        results: item
                    }
                },
                cache: false,
            },
            minimumInputLength: 3,
            // templateResult: formatRepo,
            // templateSelection: formatRepoSelection
        });
    }

    function showBonusPerhitungan(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        var perusahaan = $("#perusahaan").val()
        $.ajax({
            url         :   "{{route('showBonusPerhitungan')}}",
            data        :   {
                tahun : thn,
                bulan : bln,
                perusahaan : perusahaan,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tahun').value = '2018';
            }
        });
    }

    function createBonusPerhitungan(){
        var tgl = $("#tgl_transaksi").val()
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        var perusahaan = $("#perusahaan").val()
        console.log(tgl, bln, thn, perusahaan)
        if(tgl=="" || bln==null || thn==null || perusahaan==null){
            alert('Pastikan isi field bulan, tahun, tanggal transaksi, dan perusahaan');
        }else{
            $.ajax({
                url         :   "{{route('createBonusPerhitungan')}}",
                data        :   {
                    tgl : tgl,
                    tahun : thn,
                    bulan : bln,
                    perusahaan : perusahaan,
                },
                type		:	"GET",
                dataType    :   "html",
                success		:	function(data){
                    $("#tblBonus").html(data);
                    $('#tgl_transaksi').attr('disabled','true');
                    $('#bulan').attr('disabled','true');
                    $('#tahun').attr('disabled','true');
                    $('#perusahaan').attr('disabled','true');
                },
                error       :   function(data){
                    document.getElementById('tahun').value = '2016';
                }
            });
        }
    }

    function showBonusPenerimaan(){
        var tgl = $("#tgl_transaksi").val()
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        var rekening = $("#coa").val()

        if(rekening == '1.1.3.3'){
            var supplier = $("#supplier").val()
        }else{
            var supplier = 0
        }
        console.log(rekening, supplier);
        $.ajax({
            url         :   "{{route('showBonusPenerimaan')}}",
            data        :   {
                tgl : tgl,
                tahun : thn,
                bulan : bln,
                rkng : rekening,
                splr : supplier,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tgl_transaksi').value = '1945-08-17';
            }
        });
    }

    function createBonusPenerimaan(){
        var tgl = $("#tgl_transaksi").val()
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        var rekening = $("#coa").val()
        var rek = $("#rekening").val()

        // console.log(thn);
        if(tgl=="" || bln==null || thn==null || rek==null){
            alert('Pastikan isi field Bulan, Tahun, Tanggal Transaksi, dan Rekening Bank Tujuan');
        }else{
            if(rekening == '1.1.3.3'){
                var supplier = $("#supplier").val()
            }else{
                var supplier = 0
            }
            $.ajax({
                url         :   "{{route('createBonusPenerimaan')}}",
                data        :   {
                    tgl : tgl,
                    tahun : thn,
                    bulan : bln,
                    rkng : rekening,
                    splr : supplier,
                },
                type		:	"GET",
                dataType    :   "html",
                success		:	function(data){
                    $("#tblBonus").html(data);
                    $('#tgl_transaksi').attr('disabled','true');
                    $('#bulan').attr('disabled','true');
                    $('#tahun').attr('disabled','true');
                    $('#rekening').attr('disabled','true');
                    $('#supplier').attr('disabled','true');
                },
                error       :   function(data){
                    document.getElementById('tgl_transaksi').value = '1945-08-17';
                }
            });
        }
    }

    function showBonusTopup(){
        var tgl = $("#tgl_transaksi").val()
        var rekening = $("#rekening").val()
        $.ajax({
            url         :   "{{route('showBonusTopup')}}",
            data        :   {
                tgl : tgl,
                rkng : rekening,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tgl_transaksi').value = '1945-08-17';
            }
        });
    }

    function createBonusTopup(){
        var tgl = $("#tgl_transaksi").val()
        var rekening = $("#rekening").val()
        if(tgl=="" || rekening==null){
            alert('Pastikan isi field Tanggal Transaksi, dan Sumber Rekening');
        }else{
            $.ajax({
                url         :   "{{route('createBonusTopup')}}",
                data        :   {
                    tgl : tgl,
                    rkng : rekening,
                },
                type		:	"GET",
                dataType    :   "html",
                success		:	function(data){
                    $("#tblBonus").html(data);
                    $('#tgl_transaksi').attr('disabled','true');
                    $('#rekening').attr('disabled','true');
                },
                error       :   function(data){
                    document.getElementById('tgl_transaksi').value = '1945-08-17';
                }
            });
        }
    }

    function showLaporanBonus(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        $.ajax({
            url         :   "{{route('showLaporanBonus')}}",
            data        :   {
                bulan : bln,
                tahun : thn,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tahun').value = '2018';
            }
        });
    }

    function showLaporanBonusGagal(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        var perusahaan = $("#perusahaan").val()
        $.ajax({
            url         :   "{{route('showLaporanBonusGagal')}}",
            data        :   {
                bulan : bln,
                tahun : thn,
                prshn : perusahaan,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tahun').value = '2018';
            }
        });
    }

    function showEstimasiBonus(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        $.ajax({
            url         :   "{{route('showEstimasiBonus')}}",
            data        :   {
                bulan : bln,
                tahun : thn,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tahun').value = '2018';
            }
        });
    }

    function checkBonus(id){
        bonus = $('#bonus'+id).val();

        if(bonus == NaN || bonus == null || bonus == ""){
            bonus=0;
        }else{
            bonus = bonus;
            // console.log(subharga);
            $('#bonus'+id).val(bonus);
        }
        checkTotal();
    }

    function checkTotal(){
        var bonusapa = $('#bonusapa').val();
        var rows= $('#editable-datatable tbody tr.trow').length;
        var totalharga = 0;
        var bonus = $("input[name='bonus[]']").map(function(){return $(this).val();}).get();
        // console.log(rows)
        for(i=0;i<rows;i++){
            b = parseInt(bonus[i]);

            if(b == NaN || b == ""){
                b = 0;
            }

            totalharga = totalharga + parseInt(b);
            // console.log(totalharga)
        }

        if(bonusapa=="perhitungan"){
            var selisih = totalharga - parseInt($('#estimasi_bonus').val());
        }else if(bonusapa=="pembayaran"){
            var selisih = parseInt($('#bonus_tertahan').val()) - totalharga;
        }

        // console.log(totalharga)
        $('#total_bonus').val(totalharga);
        $('#selisih_bonus').val(selisih);
    }

    function checkEstimasiBonus(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        var perusahaan = $("#perusahaan").val()
        var id_jurnal = $("#id_jurnal_lama").val()
        var bonusapa = $("#bonusapa").val()
        // console.log(bln,thn,perusahaan);
        if(bonusapa=="edit"){
            $.ajax({
                url         :   "{{route('checkEstimasiBonus')}}",
                data        :   {
                    tahun : thn,
                    bulan : bln,
                    perusahaan_id : perusahaan,
                    id_jurnal : id_jurnal,
                    bonusapa : bonusapa,
                },
                type		:	"GET",
                dataType    :   "json",
                success		:	function(data){
                    console.log(data)
                    $("#estimasi_bonus").val(data);
                    checkTotal();
                },
                error       :   function(data){
                    document.getElementById('tahun').value = '2018';
                }
            });
        }
    }

    function checkNull(){
        var bonusapa = $("#bonusapa").val()
        var tgl = $("#tgl_transaksi").val()
        var rows= $('#editable-datatable tbody tr.trow').length;

        if(bonusapa == "perhitungan"){
            var bln = $("#bulan").val()
            var thn = $("#tahun").val()
            var perusahaan = $("#perusahaan").val()

            if(tgl=="" || bln==null || thn==null || perusahaan==null || rows == 0){
                alert('Pastikan isi field Bulan, Tahun, Tanggal Transaksi, Nama Perusahaan, dan Isi tabel Bonus Member');
                return false;
            }
            return true;

        }else if(bonusapa == "pembayaran"){
            var bln = $("#bulan").val()
            var thn = $("#tahun").val()
            var rekening = $("#coa").val()

            if(tgl=="" || bln==null || thn==null || rekening==null || rows == 0){
                alert('Pastikan isi field Bulan, Tahun, Tanggal Transaksi, dan Rekening Bank Tujuan');
                return false
            }
            return true
        }else if(bonusapa == "topup"){
            var rekening = $("#rekening").val()

            if(tgl=="" || rekening== null || rows == 0){
                alert('Pastikan isi field Tanggal Transaksi dan Sumber Rekening');
                return false
            }
            return true
        }
    }

    function getDetail(id, bonusapa){
        console.log(id+bonusapa)
        $.ajax({
            url : "{{ route('bonus.show', ['id'=>1]) }}",
            type : "get",
            dataType: 'json',
            data:{
                id_jurnal: id,
                bonusapa: bonusapa,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
