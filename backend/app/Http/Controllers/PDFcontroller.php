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
                    
               // LOG::info('Texto extraído: ' . substr($texto, 0, 500) . '...'); // Log dos primeiros 200 caracteres

        

        $caracteresControlo = preg_match_all('/[\x07-\x0D\x0E-\x1F]/', $texto);
        $total = mb_strlen(trim($texto));
        $ratioControlo = $total > 0 ? $caracteresControlo / $total : 0;

        \Log::info("Ratio caracteres de controlo: {$ratioControlo}");

        if (
        empty(trim($texto)) || $ratioControlo > 0.05  // MAIS DE 5% ILEGIVEL
        ) {
        \Log::warning('Texto ilegível, a usar OCR para: ' . $file->getPathname());
        $texto = $this->extrairTextoComOCR($file->getPathname());
        }

        $texto = str_replace('|', '', $texto); // Limpar "|" que possam aparecer na extração
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

        if (strpos($line, 'Processado por Computador') !== false) {
            $start = false;
            continue;
        }
        //Log::info('Linha capturada: ' . $line);
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

            //--------------------------------------------------------

            foreach ($lines as $line) {
                 $line = trim($line);
                if ($line === '') continue;

                 $pattern = '/^(\d+)\s+([A-ZÀÁÂÃÄÉÊÍÓÔÕÚÇ\s]+?)\s+(\d{4}\/\d{2})\s+([A-Z0-9])\s+([\d.]+)\s+([\d.]+)$/';

                if (preg_match($pattern, $line, $matches)) {
                        $cols = [
                            $matches[1],
                            trim($matches[2]),
                            $matches[3],
                            $matches[4],
                            $matches[5],
                            $matches[6],
                        ];
                    } else {
                        $cols = preg_split('/\s{2,}/', $line);
                        $cols = array_pad($cols, 6, '');
                    }

                    $rows[] = $cols;
                }

            // -------------------------------------------------------
            
            //VERIFICAR
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

   private function extrairTextoComOCR(string $pdfPath): string
{
    \Log::info('Iniciando OCR para: ' . $pdfPath);
    
    // Verificar se o ficheiro existe
    if (!file_exists($pdfPath)) {
        \Log::error('OCR: Ficheiro não existe: ' . $pdfPath);
        return '';
    }

    $prefix = '/tmp/pagina_' . uniqid();
    $texto  = '';

    // Converter PDF em imagens
    $cmd = "pdftoppm -r 300 {$pdfPath} {$prefix} 2>&1";
    $res = shell_exec($cmd);
   // \Log::info('pdftoppm resultado: ' . $res);

    $paginas = glob("{$prefix}*.ppm");
    //\Log::info('Páginas encontradas: ' . count($paginas));

    if (empty($paginas)) {
        \Log::error('OCR: Nenhuma imagem gerada');
        return '';
    }

    // Processar cada página com Tesseract
    foreach ($paginas as $pagina) {
        $output = '/tmp/resultado_' . uniqid();

        $cmd2 = "tesseract {$pagina} {$output} -l por --psm 6";
        $res2 = shell_exec($cmd2);
        \Log::info('Tesseract resultado (' . basename($pagina) . '): ' . $res2);

        if (file_exists("{$output}.txt")) {
            $conteudo = file_get_contents("{$output}.txt");
            \Log::info('Texto extraído (primeiros 100 chars): ' . substr($conteudo, 0, 100));
            $texto .= $conteudo;
            unlink("{$output}.txt");
        } else {
            \Log::error('Output não criado: ' . $output . '.txt');
        }

        unlink($pagina);
    }

    \Log::info('OCR concluído. Total chars: ' . strlen($texto));
    return $texto;
}
}