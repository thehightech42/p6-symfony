<?php
namespace App\Service;

class GestionFile
{
    private $path;

    public function getPath()
    {
        return $this->path;
    }

    public function move($file, $uploadDir)
    {
        // var_dump('test2');
        $extension = explode('.', $file['name']);
        $newPath = 'uploads/img/'.md5(uniqid()).'.'.end($extension);
        $newFileName = str_replace('\\', '/', $uploadDir.'/'.$newPath);

        try{
            move_uploaded_file($file, $newFileName);
            return $this->path = $newPath;
        }catch (\Execption $e) {
            var_dump('Error move');
        }

        
    }

}