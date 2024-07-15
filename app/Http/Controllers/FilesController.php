<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Drivers\Gd\Driver;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Database\Eloquent\Collection;

class FilesController extends Controller {
    /**
     * Used to upload a file to google cloud storage.
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string|null $dir
     * @param string|null $name
     * @return string|false
     */
    public function uploadFile(UploadedFile $file, $dir = 'images', $name = null, $scale = true): string|false {

        if ($file->extension() == 'pdf') {
            $pdf = new Pdf($file);
            $pdf->saveImage(storage_path('app/public/temp.jpg'));
            $file = new UploadedFile(storage_path('app/public/temp.jpg'), 'temp.jpg');
        }

        $name = collect([
            $name ?: Str::random(25),
            $file->extension()
        ])->join('.');

        // scale the image down to save storage space
        if ($scale) {
            $manager = new ImageManager(new Driver());
            $img = $manager->read($file->path());
            $img->scale(height: 1000);
            $img->save();
        }

        $path = $file->storeAs($dir, $name, 'gcs');
        $exists = Storage::disk('gcs')->exists($path);

        return $exists ? $path : false;
    }

    /**
     * Delete a file from google cloud storage.
     * 
     * @param string
     * @return void
     */
    public function deleteFile(String $path) {
        Storage::disk('gcs')->delete($path);
    }

    /**
     * Used to download the file from google cloud storage.
     * 
     * @param Request $request
     * @param String $path
     * @return void
     */
    public function download(Request $request) {
        return Storage::download($request->path);
    }

    /**
     * Used to convert a collection to a CSV file for export.
     * 
     * @param \Illuminate\Support\Collection $data
     * @param array $only
     * @param string $name
     * @return void
     */
    public function doExport($data, array $only, String $name = null) {
        ignore_user_abort(true);

        $name = ($name ?? 'export') . ".csv";
        $writer = SimpleExcelWriter::streamDownload($name);

        $i = 0;
        foreach ($data->lazy(100) as $item) {
            $writer->addRow($item->only($only));

            if ($i % 100 === 0)
                flush(); // Flush the buffer every 100 rows
            $i++;
        }

        return $writer->toBrowser();
    }
}
