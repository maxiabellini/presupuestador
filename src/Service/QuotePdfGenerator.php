<?php

namespace App\Service;

use App\Entity\Quote;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

final class QuotePdfGenerator
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function generate(Quote $quote): string
    {
        $html = $this->twig->render('quote/pdf.html.twig', [
            'quote' => $quote,
            'generatedAt' => new \DateTimeImmutable(),
        ]);

        $options = new Options([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => false,
            'isPhpEnabled' => false,
            'isJavascriptEnabled' => false,
        ]);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
