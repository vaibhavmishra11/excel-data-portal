<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Log;

class ClientImport implements ToCollection
{
    private $isForRemoveDuplicates;

    public function __construct($isForRemoveDuplicates = false)
    {
        $this->isForRemoveDuplicates = $isForRemoveDuplicates;
    }

    public function collection(Collection $rows)
    {
        // Validation
        Log::debug($this->isForRemoveDuplicates);
        $expectedColumnsNames = ['First Name', 'Last Name', 'Email'];
        if (count($rows[0]) !== count($expectedColumnsNames)) {
            throw new \Exception('Column Count does not match');
        }

        for ($i = 0; $i < count($rows[0]); $i++) {
            if ($rows[0][$i] !== $expectedColumnsNames[$i]) {
                throw new \Exception('Invalid fields found: ' . $rows[0][$i]);
            }
        }
        Log::debug('Validation passed');

        // Import tasks in database
        if (!$this->isForRemoveDuplicates) {
            Log::debug("Import tasks in database");
            $rowCount = count($rows);
            $rowData = $rows->toArray();
            $lastId = Client::max('id') ?? 0;

            for ($i = 1; $i < $rowCount; $i++) {
                $clientData = new Client([
                    "id" => $lastId + $i,
                    "first_name" => $rowData[$i][0],
                    "last_name" => $rowData[$i][1],
                    "email" => $rowData[$i][2],
                ]);
                $clientData->save();
            }
            Log::debug("File imported successfully");
        }

        return $rows;
    }
}
