<!DOCTYPE html>
<html>
<head>
    @if($jenis=="perhitungan")
        <title>Perhitungan Bonus Royal Warehouse</title>
    @elseif($jenis=="pembayaran")
        <title>Penerimaan Bonus Royal Warehouse</title>
    @elseif($jenis=="topup")
        <title>Top Up Bonus Royal Warehouse</title>
    @endif
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
        @if($jenis=="perhitungan")
            <h3>Daftar Member Royal Warehouse yang perhitungan bonusnya gagal di upload (Bulan bonus {{ $bulan }} {{ $tahun }})</h3>
        @elseif($jenis=="pembayaran")
            <h3>Daftar Member Royal Warehouse yang penerimaan bonusnya gagal di upload (Bulan bonus {{ $bulan }} {{ $tahun }})</h3>
        @elseif($jenis=="topup")
            <h3>Daftar Member Royal Warehouse yang topup bonusnya gagal di upload ({{ $tgl }})</h3>
        @endif
	</center>

	<table class='table table-bordered'>
		<thead>
			<tr>
				<th>No</th>
                <th>Nama</th>
                <th>KTP</th>
                @if($jenis=="perhitungan")
                    <th>NO ID</th>
                @endif
				<th>No Rekening</th>
				<th>Bonus</th>
			</tr>
		</thead>
		<tbody>
            @php
                $i=1
            @endphp
			@foreach($member as $m)
			<tr>
				<td>{{ $i++ }}</td>
                <td>{{$m['Nama']}}</td>
                <td>{{$m['No KTP']}}</td>
                @if($jenis=="perhitungan")
                    <td>{{$m['No ID']}}</td>
                @endif
                <td>{{$m['No Rekening']}}</td>
                <td>{{$m['Bonus']}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

</body>
</html>
