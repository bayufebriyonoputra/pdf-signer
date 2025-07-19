<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Email Notifikasi Tagihan</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 20px; color: #111827; line-height: 1.6;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; margin: auto; background-color: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 0 6px rgba(0,0,0,0.05);">
    <tr>
      <td>
        <p style="margin: 0 0 12px; font-size: 16px;">Selamat Pagi,</p>
        <p style="margin: 0 0 24px; font-size: 16px;">Silakan terlampir Tanda Terima Invoice Tagihan.<br />Terima kasih.</p>

        <div style="border-left: 4px solid #dc2626; padding-left: 16px; margin: 24px 0; background-color: #fef2f2; padding: 12px 16px;">
          <p style="margin: 0; color: #dc2626; font-style: italic; font-size: 15px;">
            <strong>{{ $details['custom_message'] }}</strong>

          </p>
        </div>

        <div style="margin-top: 36px;">
          <p style="margin: 0; font-weight: bold; color: #9333ea; font-size: 18px;">Risa Dwi S.</p>
          <p style="margin: 4px 0 12px; font-weight: bold; font-size: 17px; color: #c026d3;">Purchasing Section</p>

          <p style="margin: 0;">
            PT. SURABAYA AUTOCOMP INDONESIA<br />
            Ngoro Industri Persada Kav. T-1<br />
            P.O. BOX 11 Ngoro 61385<br />
            Mojokerto, East Java – Indonesia
          </p>
          <p style="margin: 8px 0 0;"><strong>Phone:</strong> 0321-6817395 / 0811-3119-6008<br />
          <strong>Ext:</strong> 5301</p>
        </div>
      </td>
    </tr>
  </table>
</body>
</html>
