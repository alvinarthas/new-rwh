<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sales Order Invoice</title>
    <style>
    .clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #5D6975;
  text-decoration: underline;
}

body {
  position: relative;
  width: 21cm;  
  height: 29.7cm; 
  margin: 0 auto; 
  color: #001028;
  background: #FFFFFF; 
  font-family: Arial, sans-serif; 
  font-size: 12px; 
  font-family: Arial;
}

header {
  padding: 10px 0;
  margin-bottom: 30px;
}

#logo {
  text-align: center;
  margin-bottom: 10px;
}

#logo img {
  width: 90px;
}

h1 {
  border-top: 1px solid  #5D6975;
  border-bottom: 1px solid  #5D6975;
  color: #5D6975;
  font-size: 2.4em;
  line-height: 1.4em;
  font-weight: normal;
  text-align: center;
  margin: 0 0 20px 0;
  background: url("{{ asset('assets/images/dimension.png') }}");
}
h2 {
  color: black;
  font-size: 2.2em;
  line-height: 1.4em;
  font-weight: normal;
  text-align: center;
  margin: 0 0 22px 0;
}

#project {
  float: left;
}

#project span {
  color: #5D6975;
  text-align: right;
  width: 52px;
  margin-right: 10px;
  display: inline-block;
  font-size: 0.8em;
}

#company {
  float: right;
  text-align: right;
}

#company2 {
  position: relative;
  left: 533px;
  top: 82px;
}

#project div,
#company div {
  white-space: nowrap;        
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: -60px;
}

table tr:nth-child(2n-1) td {
  background: #F5F5F5;
}

table th,
table td {
  text-align: center;
}

table th {
  padding: 5px 20px;
  color: #5D6975;
  border-bottom: 1px solid #C1CED9;
  white-space: nowrap;        
  font-weight: bold;
}

table .service,
table .desc {
  text-align: left;
}

table td {
  padding: 20px;
  text-align: right;
}

table td.service,
table td.desc {
  vertical-align: top;
}

table td.unit,
table td.qty,
table td.total {
  font-size: 1.2em;
}

table td.grand {
  border-top: 1px solid #5D6975;
}

p{
  font-size: 1.3em;
}

#notices .notice {
  color: #5D6975;
  font-size: 1.2em;
}

footer {
  color: #5D6975;
  width: 100%;
  height: 30px;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #C1CED9;
  padding: 8px 0;
  text-align: center;
}
    </style>
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="{{ asset('assets/images/logo.png') }}"><h2>ROYAL WAREHOUSE HERBAL STORE</h2>
      </div>
      <h1>SALES INVOICE</h1>
      <div id="company" class="clearfix">
          <p>Customer Name : Nanang Suryana</p>
      </div>
      <div id="project">
        <p>Transaction ID : 611</p>
        <p>Transaction DATE : 2016-12-30</p>
      </div>
    </header>
    <main>
      <table>
        <thead>
          <tr>
            <th class="service">Item Name</th>
            <th class="service">Qty</th>
            <th style="text-align:center">Unit</th>
            <th style="text-align:center">Price</th>
            <th style="text-align:center">Sub Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="service">Frutablend @30 kapsul</td>
            <td class="service">80</td>
            <td style="text-align:center">Btl</td>
            <td style="text-align:center">Rp. 119,300</td>
            <td style="text-align:center">Rp. 9.119,300</td>
          </tr>
          <tr>
            <td colspan="4" class="grand total">TOTAL TRANSAKSI</td>
            <td class="grand total" style="text-align:center">Rp. 19.119,300</td>
          </tr>
        </tbody>
      </table>
      <div id="company2" class="clearfix">
          <p>Nama Pengirim :</p>
          <p>Tanda Tangan :</p>
      </div>
      <div id="project">
          <p>Nama Penerima : </p>
          <p>Tanda Tangan : </p>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>