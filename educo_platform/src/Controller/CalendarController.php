<?php
// src/Controller/CalendarController.php

namespace App\Controller;

use App\Event\SetDataEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CalendarController extends AbstractController
{

    #[Route('/fc-load-events', name: 'fc_load_events',methods: ['POST'])]

    public function loadEvents(EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        // Create a new SetDataEvent to fetch the events
        $setDataEvent = new SetDataEvent([]);

        // Dispatch the event to fill the event data
        $eventDispatcher->dispatch($setDataEvent);

        // Return the events as a JSON response
        return new JsonResponse($setDataEvent->getEvents());
    }

    #[Route('/exams_schedule', name: 'exam_schedule', methods: ['GET'] )]

    public function showCalendar(): Response
    {
        return $this->render('exams/exam_schedule.html.twig');
    }
}
