<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Path to the legacy SQL files
        $sqlPath = base_path('flatly1.sql');
        
        // Fallback to flatlyby_main.sql if flatly1.sql is not present
        if (!file_exists($sqlPath)) {
            $sqlPath = base_path('flatlyby_main.sql');
        }

        if (!file_exists($sqlPath)) {
            Log::warning("Legacy SQL dump file not found at base path. Skipping populating cities.");
            return;
        }

        Log::info("Starting to populate listing cities from SQL dump: {$sqlPath}");
        
        $listings = $this->parseSqlFile($sqlPath);
        
        if (empty($listings)) {
            Log::warning("No listings parsed from the SQL dump.");
            return;
        }

        Log::info("Parsed " . count($listings) . " listings. Updating listings table...");

        $updatedCount = 0;
        foreach ($listings as $id => $city) {
            if (empty($city)) {
                continue;
            }

            // Check if listing exists in the new database
            $exists = DB::table('listings')->where('id', $id)->exists();
            if ($exists) {
                DB::table('listings')
                    ->where('id', $id)
                    ->update(['city' => $city]);
                $updatedCount++;
            }
        }

        Log::info("Successfully updated city values for {$updatedCount} listings.");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting city changes is not strictly required since it is a data patch.
    }

    /**
     * Parse SQL file line-by-line using a robust state machine to avoid issues
     * with parentheses or quotes inside description/title fields.
     */
    private function parseSqlFile(string $file): array
    {
        $handle = fopen($file, 'r');
        if (!$handle) {
            return [];
        }

        $listings = [];
        $accumulating = false;
        $sqlBuffer = '';

        while (($line = fgets($handle)) !== false) {
            if (strpos($line, 'INSERT INTO `listings`') !== false) {
                $accumulating = true;
                $sqlBuffer = $line;
                continue;
            }

            if ($accumulating) {
                $sqlBuffer .= $line;
                if (strpos($line, ';') !== false) {
                    $accumulating = false;
                    
                    $rows = $this->extractRows($sqlBuffer);
                    foreach ($rows as $row) {
                        $fields = $this->splitRow($row);
                        if (count($fields) >= 12) {
                            $id = (int) $fields[0];
                            $city = $fields[10];
                            
                            if ($city === 'NULL') {
                                $city = null;
                            }
                            
                            $listings[$id] = $city;
                        }
                    }
                    $sqlBuffer = '';
                }
            }
        }

        fclose($handle);
        return $listings;
    }

    /**
     * Extracts values blocks (rows) from the accumulated INSERT statement.
     */
    private function extractRows(string $sql): array
    {
        $length = strlen($sql);
        $in_string = false;
        $escape = false;
        $current_row = '';
        $rows = [];

        $valuesPos = stripos($sql, 'VALUES');
        if ($valuesPos === false) {
            return [];
        }
        
        $start = $valuesPos + 6;

        for ($i = $start; $i < $length; $i++) {
            $char = $sql[$i];

            if ($escape) {
                $current_row .= $char;
                $escape = false;
                continue;
            }

            if ($char === '\\') {
                $current_row .= $char;
                $escape = true;
                continue;
            }

            if ($char === "'") {
                $in_string = !$in_string;
                $current_row .= $char;
                continue;
            }

            if (!$in_string) {
                if ($char === '(') {
                    $current_row = '';
                    continue;
                }
                if ($char === ')') {
                    $rows[] = $current_row;
                    $current_row = '';
                    continue;
                }
                if ($char === ';') {
                    break;
                }
            }

            $current_row .= $char;
        }
        return $rows;
    }

    /**
     * Splits a row values string into tokens/fields, respecting string quotes.
     */
    private function splitRow(string $row): array
    {
        $length = strlen($row);
        $in_string = false;
        $escape = false;
        $current_field = '';
        $fields = [];

        for ($i = 0; $i < $length; $i++) {
            $char = $row[$i];

            if ($escape) {
                $current_field .= $char;
                $escape = false;
                continue;
            }

            if ($char === '\\') {
                $current_field .= $char;
                $escape = true;
                continue;
            }

            if ($char === "'") {
                $in_string = !$in_string;
                continue;
            }

            if ($char === ',' && !$in_string) {
                $fields[] = trim($current_field);
                $current_field = '';
                continue;
            }

            $current_field .= $char;
        }
        $fields[] = trim($current_field);
        return $fields;
    }
};
