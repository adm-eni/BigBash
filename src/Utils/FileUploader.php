<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function upload(UploadedFile $file, string $directory, string $name = ""): string
    {
        //création de son nom
        $newFilename = ($name ? $name . '-' : '') . uniqid() . '.' . $file->guessExtension();
        //sauvegarde dans le bon répertoire en le renomant
        $file->move($directory, $newFilename);

        return $newFilename;
    }

    public function delete(string $directory, string $filename): void
    {

        if (file_exists($directory . DIRECTORY_SEPARATOR . $filename)) {
            unlink($directory . DIRECTORY_SEPARATOR . $filename);
        }
    }

}