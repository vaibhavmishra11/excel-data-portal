<?php

namespace App\Http\Controllers;

use App\Exports\ClientExport;
use App\Imports\ClientImport;
use App\Jobs\ProcessClientDataJob;
use App\Mail\DuplicateRemovedNotification;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Log;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function removeDuplicates(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);


        try {
            $file = $request->file('file');
            Excel::import(new ClientImport(true), $file);
            $clientData = Excel::toArray([], $file)[0];
            $headers = array_shift($clientData);
            $newClientData = [];
            $importedCount = 0;
            $duplicateCount = 0;
            $rejectedCount = 0;

            foreach ($clientData as $row) {
                $email = $row[2];
                $existingClient = Client::where('email', $email)->exists();

                if (!$existingClient) {
                    array_push($newClientData, $row);
                    $importedCount++;
                } else {
                    $duplicateCount++;
                }
            }
            $rejectedCount = count($clientData) - $importedCount;
            session(['import_summary' => [
                'importedCount' => $importedCount,
                'duplicateCount' => $duplicateCount,
                'rejectedCount' => $rejectedCount,
            ]]);

            $newClientData = array_merge([$headers], $newClientData);
            return Excel::download(new ClientExport($newClientData), 'removed_clients.xlsx');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error during import: ' . $e->getMessage()], 422);
        }
    }
    private function sendDuplicateRemovedNotification($importSummary)
    {
        $user = Auth::user();

        Mail::to($user->email)->send(new DuplicateRemovedNotification($importSummary));

    }


    public function saveData(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file')->store('files');

            // Dispatch the job to process the Excel file in the background
            ProcessClientDataJob::dispatch($file);
            $importSummary = session('import_summary', []);
            $this->sendDuplicateRemovedNotification($importSummary);

            Log::debug(request()->all());

            return response()->json(['message' => 'File uploaded. Data will be processed in the background.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error during import: ' . $e->getMessage()], 422);
        }
    }

}


