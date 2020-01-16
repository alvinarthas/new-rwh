<!DOCTYPE html>
<html>
<head>
    <title>Stock Controlling Royal Warehouse {{$tgl}}</title>
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
        <h3>Stock Controlling Royal Warehouse {{$tgl}}</h3>
	</center>

	<table class='table table-bordered'>
		<thead>
			<tr>
                <th>No</th>
                <th>Supplier</th>
                <th>Product ID</th>
                <th>Nama Produk</th>
                <th>Indent</th>
				<th>di Gudang</th>
                <th>milik Customer</th>
                <th>Nett</th>
			</tr>
		</thead>
		<tbody>
            @php
                $i=1
            @endphp
			@foreach($product as $p)
			<tr>
				<td>{{ $i++ }}</td>
                <td>{{$p['Supplier']}}</td>
                <td>{{$p['Product ID']}}</td>
                <td>{{$p['Nama Produk']}}</td>
                <td>{{$p['Indent']}}</td>
                <td>{{$p['di Gudang']}}</td>
                <td>{{$p['milik Customer']}}</td>
                <td>{{$p['Nett']}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

</body>
</html>
