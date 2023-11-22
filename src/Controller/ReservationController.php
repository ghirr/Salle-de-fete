<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/reservation/new', name: 'reservation_new')]
    public function new(Request $request): Response
    {
        $reservation = new Reservation();

        // Récupérer le nom de la salle à partir de la requête ou de toute autre source
        $nomSalle = $request->query->get('Nom_de_la_salle');
        // Passer le nom de la salle à la création du formulaire
        $form = $this->createForm(ReservationType::class, $reservation, [
            'nomSalle' => $nomSalle,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le nom de la salle à partir des données du formulaire
            $nomSalle = $form->get('nomSalle')->getData();
            // Définir le nom de la salle dans l'objet de réservation
            $reservation->setNomSalle($nomSalle);

            // Handle the submitted form data and persist the reservation
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();

            // Flash a success message to the user
            $this->addFlash('success', 'La réservation a été effectuée avec succès !');

            // Redirect to the list of salles or any other page
            return $this->redirectToRoute('app_liste', ['reservationRedirect' => true]);
        }

        return $this->render('reservation/reserver.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
