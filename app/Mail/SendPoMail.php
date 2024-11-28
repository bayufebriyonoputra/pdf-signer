<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $details;
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "PO-" . $this->details['noPo'] . ' ' . $this->details['supplier'] . ' NO-REPLY',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.send_po',
            with: $this->details,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        foreach ($this->details['attachments'] as $attachment) {
            $attachments[] = Attachment::fromPath($attachment);
        }

        $pdfContent = $this->details['attachment_po'];
        //make fpdi instance
        $pdf = new Fpdi();
        // Menyimpan konfigurasi dasar PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Electronics PO');
        $pdf->SetTitle('Po Digitaly Signed');

        try{
            $pageCount = $pdf->setSourceFile($pdfContent);

            // Import setiap halaman dari PDF asli
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $pdf->AddPage();
                $tplId = $pdf->importPage($pageNo);
                $pdf->useTemplate($tplId, 0, 0, null, null, true);
            }

            $pdf->SetProtection(
                [
                    'copy',        // Izinkan menyalin konten
                    'modify',      // Izinkan modifikasi
                ],
                'posai123',   // Password pengguna (user password)
                'bwt123'   // Password pemilik (owner password)
            );
            //Letakkan Pdf Password sementara
            $pdf->Output(storage_path('app/public/Lampiran PO.pdf'), 'F');
            $attachments[] = Attachment::fromPath(storage_path('app/public/Lampiran PO.pdf'));
        }catch(Exception $e){
            //Atasi jika fpdi tidak bisa baca karena file terlanjur di password
            $attachments[] = Attachment::fromPath($pdfContent);
        }

        return $attachments;
    }
}
