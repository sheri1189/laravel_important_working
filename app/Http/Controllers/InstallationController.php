<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstallationController extends Controller
{
    public function install(Request $request)
    {
        try {
            $dbName = "testing_laravel_project";
            file_put_contents(base_path('.env'), str_replace(
                "DB_DATABASE=" . env('DB_DATABASE'),
                "DB_DATABASE=$dbName",
                file_get_contents(base_path('.env'))
            ));

            $databaseExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbName'");

            if (empty($databaseExists)) {
                DB::statement("CREATE DATABASE $dbName");
                Artisan::call('migrate:fresh');
            }else{
                Artisan::call('migrate');
            }
            return 'Installation complete! And Db Name is '.$dbName;
        } catch (\Exception $e) {
            Log::error('Installation failed: ' . $e->getMessage());
            return 'Installation failed: ' . $e->getMessage();
        }
    }
}
