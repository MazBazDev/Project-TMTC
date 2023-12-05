<?php

namespace app\core;

use app\models\File;

class Files
{
    public static function store(array $files)
    {
        $storedFiles = [];

        foreach ($files as $file) {
            $target_file = Application::$ROOT_DIR . "/storage/" . uniqid("f_");
            $ext = ".". strtolower(pathinfo($file["name"],PATHINFO_EXTENSION));

            $uploaded = move_uploaded_file($file["tmp_name"], $target_file . $ext);

            if ($uploaded) {
                $storedFiles[] = File::create([
                    "name" => $file["name"],
                    "path" => $target_file,
                    "ext" => $ext,
                ]);
            }
        }

        return $storedFiles;
    }

    public static function delete($id)
    {
        $file = File::where(["id", $id]);
        return $file->delete();
    }
}