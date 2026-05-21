<?php

namespace App\Http\Controllers;
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
                'pdf' => 'required|mimes:pdf|max:10240', // Apenas ficheiros PDF, máximo 10MB
            ]);

            // Extrair texto do ficheiro
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $text = $pdf->getText();
                
                $text = $this->decodeHexAscii($text);
                
                // Limpar whitespace
                $text = trim($text);
                
                if ($request->input('export') === 'true' || $request->input('export') === '1') {
                    return $this->exportToExcel($text, $file->getClientOriginalName());
                }

                return response()->json([
                    'message' => 'Ficheiro processado com sucesso!',
                    'text' => $text
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Erro ao processar PDF: ' . $e->getMessage()
                ], 400);
            }
        } else {
            return response()->json(['message' => 'Nenhum ficheiro enviado.'], 400);
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
            $lines = explode("\n", $text);
            $rows = array_map(function ($line, $index) {
                return [$index + 1, $line];
            }, $lines, array_keys($lines));

            // Exportar como Excel
            $response = Excel::download(
                new PdfTextExport($rows),
                'extracted_text.xlsx'
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

    /**
     * Decodificar texto codificado em hex ASCII
     */
    private function decodeHexAscii($text)
    {
        // Converter \xHH em caracteres ASCII
        $decoded = preg_replace_callback('/\\x([0-9A-Fa-f]{2})/', function($matches) {
            return chr(hexdec($matches[1]));
        }, $text);
        
        return $decoded;
    }
}