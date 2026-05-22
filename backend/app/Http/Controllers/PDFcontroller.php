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
    // public function upload(Request $request)
    // {
    //     if ($request->hasFile('pdf')) {
    //         $file = $request->file('pdf');

    //         $request->validate([
    //             'pdf' => 'required|mimes:pdf|max:10240', // Apenas ficheiros PDF, máximo 10MB
    //         ]);

    //         // Extrair texto do ficheiro
    //         try {
    //             $parser = new Parser();
    //              $pdf = $parser->parseFile($file->getPathname());
    //             $text = $pdf->getText();
               

    //             $info = $pdf->getPages()[0]->getDataTm();

    //             $entidade = $info[3][1] ?? 'VAZIO';
    //             $morada = ($info[6][1] ?? '') . ' ' . ($info[7][1] ?? '') . ' ' . ($info[8][1] ?? '');
    //             $ss = $info[10][1] ?? 'VAZIO';
    //             $NIF = $info[18][1] ?? 'VAZIO';
    //             $estabelecimento = $info[22][1] ?? 'VAZIO';
    //             $taxa = $info[19][1] ?? 'VAZIO';
    //             $anomes = $info[23][1] ?? 'VAZIO';
    //             $dataentrega = $info[20][1] ?? 'VAZIO';
    //             $totalr = $info[24][1] ?? 'VAZIO';
    //             $totalc = $info[21][1] ?? 'VAZIO';

    //             $rows = [
    //                 ['Entidade', $entidade],
    //                 ['Morada', $morada],
    //                 ['SS', $ss],
    //                 ['NIF', $NIF],
    //                 ['Estabelecimento', $estabelecimento],
    //                 ['Taxa', $taxa],
    //                 ['Ano/Mês', $anomes],
    //                 ['Data Entrega', $dataentrega],
    //                 ['Total R', $totalr],
    //                 ['Total C', $totalc],
    //             ];
                
    //             foreach ($info as $key => $value) {
    //                 Log::info("PDF Info", [
    //                       'key' => $key,
    //                      'value' => $value,
    //                         ]);
    //                     }

    //             return response()->json([
    //                 'message' => 'Ficheiro PROCESSADO com sucesso!',
    //                 'text' => $text,
    //                 'info' => $info,
    //                 'entidade' => $entidade,
    //                 'morada' => $morada,
    //                 'ss' => $ss,
    //                 'NIF' => $NIF,
    //                 'estabelecimento' => $estabelecimento,
    //                 'taxa' => $taxa,
    //                 'anomes' => $anomes,
    //                 'dataentrega' => $dataentrega,
    //                 'totalr' => $totalr,
    //                 'totalc' => $totalc,
    //                 'rows' => $rows
    //             ], 200);
                                
    //             $text = trim($text);
                
    //             if ($request->input('export') === 'true' || $request->input('export') === '1') {
    //                 return $this->exportToExcel($text, $file->getClientOriginalName());
    //             }

    //             return response()->json([
    //                 'message' => 'Ficheiro processado com sucesso!',
    //                 'text' => $text
    //             ], 200);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'message' => 'Erro ao processar PDF: ' . $e->getMessage()
    //             ], 400);
    //         }
    //     } else {
    //         return response()->json(['message' => 'Nenhum ficheiro enviado.'], 400);
    //     }
    // }

    public function upload2(Request $request)
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

            // foreach ($lines as $line) {

            //     if (strpos($line, ':') !== false) {

            //         list($key, $value) = explode(':', $line, 2);

            //         $result[] = [
            //             'key' => trim($key),
            //             'value' => trim($value),
            //         ];
            //     }
            // }

            $start = false;
           foreach ($lines as $line) {

            $line = trim($line);

             if (!$start) {
                 if (strpos($line, 'de Seg. Social') !== false) {
                     $start = true;
                     }
                 continue;
                 }
                  if ($line === '') {
                     continue;
                }
                  if (strpos($line, 'Processado por Computador') !== false) {
                break;
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

            // Preparar linhas para exportação
            //$lines = explode("\n", $text);
            //$rows = array_map(function ($line, $index) {
             //   return [$index + 1, $line];
            //}, $lines, array_keys($lines));

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