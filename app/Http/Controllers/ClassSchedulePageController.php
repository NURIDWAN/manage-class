<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ClassSchedulePageController extends Controller
{
    public function __invoke()
    {
        $schedules = ClassSchedule::query()
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->orderBy('starts_at')
            ->get()
            ->groupBy('day_of_week')
            ->map(fn (Collection $items, string $day) => [
                'label' => $this->translateDay($day),
                'items' => $items->map(fn (ClassSchedule $schedule) => [
                    'course' => $schedule->course_name,
                    'room' => $schedule->room,
                    'time' => sprintf('%s - %s', $this->formatTime($schedule->starts_at), $this->formatTime($schedule->ends_at)),
                ]),
            ]);

        return view('schedule', [
            'schedules' => $schedules,
        ]);
    }

    protected function translateDay(string $day): string
    {
        return match ($day) {
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
            default => ucfirst($day),
        };
    }

    protected function formatTime(?string $time): string
    {
        if (! $time) {
            return '--:--';
        }

        try {
            return Carbon::createFromFormat('H:i:s', $time)->format('H:i');
        } catch (\Throwable $e) {
            return $time;
        }
    }
}
