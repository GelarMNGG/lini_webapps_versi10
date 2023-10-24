<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Helpers\Utils;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * ImageManager
 */
class ImageManager
{
    public static $path;
    public static $pathImage;
    public static $url;
    public static $subpath;
    private static $imageSize;
    private static $configuration;

    private static function init()
    {
        // self::$path     = rtrim(base_path(env('FILE_MANAGER_PATH', 'public/uploads')), '/');
        self::$path     = storage_path('app/public');
        self::$pathImage = self::$path . '/images';
        self::$subpath  = date('Y/m');
        self::$url      = rtrim(Utils::asset(env('FILE_MANAGER_URL', 'uploads')), '/');
        self::$imageSize = [
            'small'     => [100, 100],
            'thumbnail' => [300, 300],
            'medium'    => [500, 500]
        ];
        // self::$configuration = Image::configure(array('driver' => 'imagick'));
    }

    /**
     * $fileName = nama file yang diambil dari table Picture dari Column [image]
     * $type = nama folder yang ada didalam storage/images/nama_folder
     */
    public static function getImage($fileName, $type, $size = 'thumbnail')
    {
        self::init();
        $currentPath = self::$pathImage . '/' . $type . '/' . $size;

        if (!is_file($currentPath . '/' . $fileName))
            return null;

        $file = $currentPath . '/' . $fileName;
        if (file_exists($file))
            // return Utils::asset('public/images/' . $type . '/' . $size . '/' . $fileName);
            // $img = Storage::disk('public')->get('images' . '/' . $type . '/' . $size . '/' . $fileName);
            $img = Storage::url('images' . '/' . $type . '/' . $size . '/' . $fileName);
        return $img;
    }

    /**
     * $fileName = nama file yang diambil dari table Picture dari Column [image]
     * $type = nama folder yang ada didalam storage/images/nama_folder
     */
    public static function deleteImage($fileName)
    {
        self::init();
        foreach (Storage::disk('public')->directories('images', true) as $key => $dir) {
            $currentPath = self::$path . '/' . $dir . '/' . $fileName;
            if (file_exists($currentPath)) {
                unlink($currentPath);
            }
        }
    }

    /**
     * $field = file yang diambil dari form HTML
     * $type = nama folder yang ada didalam storage/images/nama_folder
     */
    public static function upload($field, $type, Request $req)
    {
        self::init();
        $currentPath = self::$pathImage . '/' . $type;

        //JIKA FOLDERNYA BELUM ADA
        if (!File::isDirectory($currentPath)) {
            //MAKA FOLDER TERSEBUT AKAN DIBUAT
            File::makeDirectory($currentPath, 0755, true);
        }

        $file     = $req->file($field);
        $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        foreach (self::$imageSize as $key => $value) {
            $tempPath = $currentPath . '/' . $key;
            if (!File::isDirectory($tempPath)) {
                File::makeDirectory($tempPath, 0755, true);
            }

            $canvas = Image::canvas($value[0], $value[1]);
            $resizeImage  = Image::make($file)->resize($value[0], $value[1], function ($constraint) {
                $constraint->aspectRatio();
            });

            $canvas->insert($resizeImage, 'center');
            $canvas->save($tempPath . '/' . $fileName);
        }

        $data['name']   = Str::title(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), ' '));
        $data['file']     = $fileName;
        $data['mime']     = $file->getClientMimeType();
        $data['size']     = $file->getSize();
        $data['success'] = true;
        return (object) $data;
    }
}
