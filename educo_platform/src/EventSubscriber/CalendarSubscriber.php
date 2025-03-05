<?php


namespace App\EventSubscriber;

use App\Event\SetDataEvent;  // Your custom SetDataEvent
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Exam;

class CalendarSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SetDataEvent::class => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(SetDataEvent $event)
    {
        $exams = $this->entityManager->getRepository(Exam::class)->findAll();

        // Prepare the events for the calendar (map Exam to Event data)
        $calendarEvents = [];
        foreach ($exams as $exam) {
            // Prepare each exam as a calendar event
            $calendarEvents[] = [
                'title' => $exam->getSubject(),
                'start' => $exam->getStartTime()->format('Y-m-d H:i:s'),
                'end' => $exam->getEndTime()->format('Y-m-d H:i:s'),
                'location' => $exam->getLocation(),
            ];
        }
        // Set the events to the SetDataEvent so they can be passed to the calendar
        $event->setEvents($calendarEvents);
    }
}
