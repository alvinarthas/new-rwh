<!DOCTYPE html>
<html>
<head>
    <title>Member Royal Warehouse</title>
</head>
<body>
    @php
        use App\BankMember;
    @endphp
	<style type="text/css">
        td, th {
			border: 1px solid #dddddd;
			text-align: left;
		    padding: 8px;
		}

        table{
			font-family: arial, sans-serif;
			border-collapse: collapse;
			width: 100%;
		}
        tr:nth-child(even) {
			background-color: #dddddd;
		}
    </style>

	<center>
		<h3>Daftar Member</h3>
	</center>

	<table class='table table-bordered'>
		<thead>
			<tr>
				<th>No</th>
				<th>Nama</th>
				<th>KTP</th>
				<th>Tabungan</th>
				<th>ATM</th>
                <th>Status Rekening</th>
                <th>Status Cetak</th>
			</tr>
		</thead>
		<tbody>
            @php
                $i=1
            @endphp
			@foreach($member as $m)
			<tr>
				<td>{{ $i++ }}</td>
                <td>{{$m->nama}}</td>
                {{-- <td>{{ $m->ktp }}</td> --}}
                <td><img src="{{ asset('assets/images/member/ktp/'.$m->scanktp) }}"  alt="logo" width="200px"></td>
                @php
                    $tabungan = BankMember::where('ktp', $m->ktp)->select('scantabungan','scanatm')->first();
                @endphp
                {{-- <td>{{ $tabungan['norek'] }}</td>
                <td>{{ $tabungan['noatm'] }}</td> --}}
                <td><img src="{{ asset('assets/images/member/tabungan/'.$tabungan['scantabungan']) }}"  alt="logo" width="200px"></td>
                <td><img src="{{ asset('assets/images/member/atm/'.$tabungan['scanatm']) }}"  alt="logo" width="200px"></td>
                @php($tabungan = BankMember::getData($m->ktp))

                @if($tabungan <> NULL)
                    <td>Aktif</td>
                @else
                    <td>Tidak Aktif</td>
                @endif

                @if($m->cetak == 0)
                    <td>Belum dicetak</td>
                @else
                    <td>Sudah Dicetak</td>
                @endif
			</tr>
			@endforeach
		</tbody>
	</table>

</body>
</html>
