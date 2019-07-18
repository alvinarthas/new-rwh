    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Detail Bonus Member</h4>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>KTP</th>
                        <th>No ID</th>
                        <th>Nama</th>
                        <th>Bonus</th>
                    </thead>

                    <tbody>
                        @php
                            $i = 1;
                            $total_bonus = 0;
                        @endphp
                        @foreach($perusahaanmember as $prm)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$prm->ktp}}</td>
                            <td>{{$prm->noid}}</td>
                            <td>{{$prm->nama}}</td>
                            @php
                                $data_bonus = $bonus->where('member_id', $prm->noid)->first();
                            @endphp
                            <td>
                                <input class="form-control number" value="{{ number_format($data_bonus['bonus'],0) }}" type="text" name="bonus{{ $i }}">
                            </td>
                        </tr>
                        @php
                            $i++;
                            $total_bonus = $total_bonus + $data_bonus['bonus'];
                        @endphp
                        @endforeach
                    </tbody>
                </table>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Total Bonus</label>
                    <div class="col-10">
                        <input type="text" class="form-control number" parsley-trigger="change" required name="total_bonus" id="total_bonus" value="@isset($total_bonus){{ $total_bonus }}@endisset" readonly="readonly">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Total Bonus Tertahan</label>
                    <div class="col-10">
                        <input type="text" class="form-control number" parsley-trigger="change" required name="total_bonus2" id="total_bonus" value="" readonly="readonly">
                    </div>
                </div>
                <input type="hidden" name="ctr" value="{{ $i }}"/>
            </div>
        </div>
    </div>

    <script>
        $(".number").divide();

        $(document).ready(function () {
            // Responsive Datatable
            $('#responsive-datatable').DataTable();
        });
    </script>
