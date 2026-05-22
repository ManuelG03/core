<?php

namespace App\Http\Controllers;
use Spatie\PdfToText\Pdf;
use App\Exports\PdfTextExport;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;


class PdfController extends Controller
{
    public function upload(Request $request)
{
    if ($request->hasFile('pdf')) {

        $file = $request->file('pdf');

        $request->validate([
            'pdf' => 'required|mimes:pdf|max:10240',
        ]);

        try {
            $texto = (new Pdf())
                ->setPdf($file->getPathname())
                ->setOptions(['layout'])
                ->text();

            $lines = explode("\n", $texto);

            $result = [];


            $start = false;
          foreach ($lines as $line) {

    $line = trim($line);

    if ($line === '') {
        continue;
    }

    // start capturing
    if (!$start) {

        if (strpos($line, 'de Seg. Social') !== false) {
            $start = true;
        }

        continue;
    }

    // stop current section but continue scanning
    if (strpos($line, 'Processado por Computador') !== false) {
        $start = false;
        continue;
    }

    $result[] = $line;
}
            // Exporta para Excel
            return response()->json([
                'message' => 'Ficheiro PROCESSADO com sucesso!',
                'text' => $texto,
                'info' => $result
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar PDF: ' . $e->getMessage()
            ], 400);
        }

    } else {
        return response()->json([
            'message' => 'Nenhum ficheiro enviado.'
        ], 400);
    }
}

    public function export(Request $request)
    {
        try {
            $text = $request->input('text', '');

            if (empty($text)) {
                return response()->json([
                    'message' => 'Erro: Nenhum texto para exportar.'
                ], 400);
            }

            if (is_array($text)) {
            $text = implode("\n", $text);
}
            $lines = explode("\n", $text);
            $rows = [];
            foreach ($lines as $line) {

                $line = trim($line);
                if ($line === '') {
                    continue;
                }
            $cols = preg_split('/\s{2,}/', $line);

            $cols = array_pad($cols, 6, '');

            $rows[] = $cols;

            }

            // Exportar como Excel
            $response = Excel::download(
                new PdfTextExport($rows),
                'extracto_de_remuneracoes.xlsx'
            );

            // Garantir headers CORS na resposta de download (BinaryFileResponse usa headers->set)
            $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');

            return $response;
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'message' => 'Erro ao exportar ficheiro: ' . $e->getMessage()
            ], 500);
        }
    }


}