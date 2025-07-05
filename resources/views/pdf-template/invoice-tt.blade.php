<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Document Receipts</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
    }

    .header-table, .info-table, .doc-table, .footer-table {
      width: 100%;
      border-collapse: collapse;
    }

    .header-table td {
      vertical-align: top;
    }

    .header-logo {
      font-weight: bold;
      font-size: 18px;
    }

    .confidential {
      border: 1px solid #000;
      padding: 5px;
      text-align: center;
      font-weight: bold;
      color: brown;
    }

    .title {
      text-align: center;
      font-weight: bold;
      font-size: 14px;
      padding: 10px 0;
    }

    .info-table td {
      padding: 3px;
    }

    .doc-table th, .doc-table td {
      border: 1px solid #000;
      padding: 5px;
      text-align: center;
    }

    .doc-table th {
      background-color: #f0f0f0;
    }

    .highlight-note {

      font-size: 11px;
      text-align: left;
      padding-left: 5px;
    }

    .total-row td {
      font-weight: bold;
    }

    .completed-box {
      border: 2px solid #007bff;
      text-align: center;
      padding: 10px;
      width: 250px;
      margin-top: 20px;
    }

    .completed-box .status {
      font-weight: bold;
      font-size: 16px;
      color: #007bff;
    }

    .completed-box .label {
      font-size: 12px;
    }

    .completed-box .date {
      color: red;
      font-weight: bold;
      font-size: 14px;
    }

    .signature-table {
      width: 100%;
      margin-top: 30px;
    }

    .signature-table td {
      vertical-align: top;
    }

    .footer-note {
      font-size: 10px;
      margin-top: 10px;
    }

    .page-number {
      font-size: 48px;
      color: #cccccc;
      text-align: center;
      position: absolute;
      top: 40%;
      left: 0;
      right: 0;
    }

    .logo {
      width: 100px;
    }

    .signature-img {
      width: 120px;
    }
  </style>
</head>
<body>

  <table class="header-table">
    <tr>
      <td class="header-logo">SAI</td>
      <td>
        <strong>PT. SURABAYA AUTOCOMP INDONESIA</strong><br>
        <em>Wiring Harness Manufacturer</em>
      </td>
      <td align="right">
        <div class="confidential">CONFIDENTIAL</div>
      </td>
    </tr>
  </table>

  <div class="title">DOCUMENT RECEIPTS</div>

  <table class="info-table">
    <tr>
      <td>Have been received from</td>
      <td>: CAHAYA SAMUDRA, CV</td>
      <td></td>
      <td align="right">No. : REC-266/PUR-SAI/V/25</td>
    </tr>
    <tr>
      <td>Courier's name</td>
      <td>: ...........................................</td>
    </tr>
  </table>

  <table class="doc-table">
    <tr>
      <th>No</th>
      <th>DOCUMENT NAME</th>
      <th>DATE</th>
      <th>AMOUNT</th>
    </tr>
    <tr>
      <td>1</td>
      <td>INV-054/002/05/2025</td>
      <td>07-05-2025</td>
      <td align="right">Rp 10.682.000,00</td>
    </tr>
    <tr class="total-row">
      <td colspan="3" align="right">TOTAL :</td>
      <td align="right">Rp 10.682.000,00</td>
    </tr>
    <tr><td colspan="4" class="highlight-note">1 no invoice mewakili total amount pada 1 PO</td></tr>
  </table>

  <table class="signature-table">
    <tr>
      <td>
        <div class="completed-box">
          <div class="status">COMPLETED</div>
          <div class="label">PAYMENT</div>
          <div class="date">18-Jun-25</div>
        </div>
      </td>
      <td align="right">
        Ngoro, 22-May-25<br><br>
        Received by : <strong>RISA PURCHASING</strong><br><br>
        <img src="stamp-yazaki.png" class="signature-img"><br>
        <strong>PT. SURABAYA AUTOCOMP INDONESIA</strong>
      </td>
    </tr>
  </table>

  <div class="footer-note">
    * Lembar warna putih untuk pengirim dan warna kuning untuk penerima
  </div>


</body>
</html>
