<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Salle;
use App\Form\SalleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;
use App\Form\StringToFileTransformer;
use Doctrine\ORM\EntityManagerInterface;


class SalleController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/ajoutersalle', name: 'app_salle')]
    public function ajoutersalle(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('image_path')->getData();
            if ($imageFile) {
                $newFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $imageFile->guessExtension();
                $imagePath = 'assets/' . $newFilename;
                $imageFile->move($this->getParameter('image_directory'), $newFilename);
                $salle->setImagePath($imagePath);
            }

            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($salle);
            $entityManager->flush();

            // Rediriger vers une autre page après la sauvegarde de la salle
            return $this->redirectToRoute('app_dash');
        }

        return $this->render('salle/ajouter.html.twig', [
            'formSalle' => $form->createView(),
        ]);
    }

    #[Route('/liste', name: 'app_liste')]
    public function salles(): Response
    {
        // Récupérer les salles depuis la base de données
        $salles = $this->entityManager->getRepository(Salle::class)->findAll();

        return $this->render('salle/salles.html.twig', [
            'salles' => $salles, // Passer les données des salles à la vue
        ]);
    }
    #[Route('/modifier/{id}', name: 'modifier_salle')]
    public function modifierSalle(Request $request, ManagerRegistry $managerRegistry, int $id): Response
{
    $entityManager = $managerRegistry->getManager();
    $salle = $entityManager->getRepository(Salle::class)->find($id);

    if (!$salle) {
        throw $this->createNotFoundException('Salle non trouvée');
    }

    // Store the current image path
    $currentImagePath = $salle->getImagePath();

    // Create the form and handle the request
    $form = $this->createForm(SalleType::class, $salle);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Check if a new image file was uploaded
        $newImageFile = $form->get('image_path')->getData();

        if ($newImageFile) {
            // Remove the old image file if it exists
            if ($currentImagePath) {
                unlink($currentImagePath);
            }

            try {
                // Generate a unique filename for the new image
                $newFilename = uniqid().'.'.$newImageFile->guessExtension();

                // Move the new image file to the target directory
                $newImageFile->move(
                    $this->getParameter('image_directory'),
                    $newFilename
                );

                // Set the new image path
                $salle->setImagePath($this->getParameter('image_directory').'/'.$newFilename);
            } catch (FileException $e) {
                // Handle the exception if there was an error moving the file
                // You can customize the error handling based on your requirements
                throw new \Exception('An error occurred while uploading the image.');
            }
        } else {
            // Keep the current image path
            $salle->setImagePath($currentImagePath);
        }

        // Save the modified salle
        $entityManager->flush();

        // Redirect to another page after the salle modification
        return $this->redirectToRoute('app_dash');
    }

    return $this->render('salle/modifier.html.twig', [
        'formSalle' => $form->createView(),
    ]);
}

    #[Route('/accueil', name: 'accueil')]
    public function accueil(): Response{
        return $this->render('salle/accueil.html.twig');
    }
    #[Route('/dash', name: 'admin')]
    public function dash(): Response
    {
        // Récupérer les salles depuis la base de données
        $salles = $this->entityManager->getRepository(Salle::class)->findAll();

        return $this->render('salle/dashadmin.html.twig', [
            'salles' => $salles, // Passer les données des salles à la vue
        ]);
    }
    #[Route('/supprimer/{id}', name: 'supprimer_salle')]
public function supprimerSalle(Request $request, ManagerRegistry $managerRegistry, int $id): Response
{
    $entityManager = $managerRegistry->getManager();
    $salle = $entityManager->getRepository(Salle::class)->find($id);

    if (!$salle) {
        throw $this->createNotFoundException('Salle non trouvée');
    }

    // Check if the request method is DELETE
    if ($request->isMethod('DELETE')) {
        // Remove the salle from the entity manager
        $entityManager->remove($salle);
        $entityManager->flush();

        // Optionally, delete the associated image file if needed
        $imagePath = $salle->getImagePath();
        if ($imagePath) {
            unlink($imagePath);
        }

        // Return a JSON response indicating successful deletion
        return $this->json(['success' => true]);
    }

    // Return a JSON response indicating the method is not allowed
    return $this->json(['error' => 'Method Not Allowed'], Response::HTTP_METHOD_NOT_ALLOWED);
}

    

}
