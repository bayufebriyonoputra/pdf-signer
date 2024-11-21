<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Template</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    color: #333;
    margin: 0;
    padding: 0;
  }
  .container {
    width: 100%;
    max-width: 600px;
    margin: 20px auto;
    background-color: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }
  .header {
    text-align: center;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
  }
  .header h1 {
    color: #1a73e8;
    font-size: 24px;
    margin: 0;
  }
  .content {
    margin-top: 20px;
    line-height: 1.8;
    font-size: 16px;
    color: #4a4a4a;
  }
  .highlight {
    background-color: #fdecea;
    color: #d32f2f;
    padding: 10px;
    border-radius: 6px;
    margin: 10px 0;
    font-weight: bold;
    display: inline-block;
  }
  .news-section {
    background-color: #f1f8ff;
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
  }
  .news-section h2 {
    color: #1a73e8;
    font-size: 18px;
    margin-top: 0;
  }
  .footer {
    margin-top: 30px;
    font-size: 0.9em;
    color: #666;
    text-align: center;
    border-top: 1px solid #eeeeee;
    padding-top: 15px;
  }
  .button {
    display: inline-block;
    padding: 12px 24px;
    margin-top: 20px;
    font-size: 16px;
    color: #ffffff;
    background-color: #1a73e8;
    text-decoration: none;
    border-radius: 5px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  }
  .button:hover {
    background-color: #0f5bbd;
  }
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>{{$details['greeting']}}</h1>
    </div>
    <div class="content">
      <p>Berikut Kami Kirimkan PO [{{$details['noPo']}}] berikut (terlampir).</p>
      <p>Mohon Informasi jika sudah menerimanya.</p>
      <p class="highlight">
        Mohon segera reply email ke <a href="mailto:purchasing02@sai.co.id" style="color: #d32f2f; text-decoration: underline;">purchasing02@sai.co.id</a> atau kirim PO confirm stamp dan tanda tangan jika email sudah diterima.
      </p>
      <div class="news-section">
        <h2>NEWS</h2>
        <p class="highlight">
          {{$details['news']}}
        </p>
      </div>
      <p>Terimakasih,</p>
      <p><strong>Setyani</strong><br>
         PURCHASING SECTION<br>
         PT SURABAYA AUTO COMP INDONESIA</p>
    </div>
    <div class="footer">
      <p>Ngoro Industri Persada Kav T-1<br>
      PO.BOX 11 Ngiri 61385<br>
      Mojokerto, East Java - Indonesia</p>
    </div>
  </div>
</body>
</html>
