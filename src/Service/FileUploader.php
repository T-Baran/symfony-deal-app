<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw new \Exception('Something went wrong, check for:' . $e);
        }
        return $fileName;
    }

    public function delete(string $fileName)
    {
        $fs = new FileSystem();
//        dd($fs->exists($this->getTargetDirectory().'/'.$fileName));
        if ($fs->exists($this->getTargetDirectory() . '/' . $fileName)) {
            $fs->remove($this->getTargetDirectory() . '/' . $fileName);
        }
        return new Response('', 200);
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}