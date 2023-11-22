<?php

// src/Form/StringToFileTransformer.php

namespace App\Form;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class StringToFileTransformer implements DataTransformerInterface
{
    private $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function transform($value)
    {
        // Transforme l'objet File en une chaîne pour l'affichage dans le formulaire
        if ($value instanceof File) {
            return $value->getFilename();
        }

        return null;
    }

    public function reverseTransform($value)
    {
        // Transforme la chaîne en un objet File lors de la soumission du formulaire
        if ($value) {
            $file = new File($this->targetDirectory.'/'.$value);
            return $file;
        }

        return null;
    }
}
