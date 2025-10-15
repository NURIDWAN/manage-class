<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ClassSchedulePageController extends Controller
{
    public function __invoke(Request $request)
    {
        $orderedDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        $rawSchedules = ClassSchedule::query()
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->orderBy('starts_at')
            ->get()
            ->groupBy('day_of_week');

        $selectedDay = strtolower($request->query('day', $this->todayKey()));
        if (! in_array($selectedDay, $orderedDays, true)) {
            $selectedDay = $this->todayKey();
        }

        $selectedSchedules = $rawSchedules->get($selectedDay, collect())->map(function (ClassSchedule $schedule) {
            return [
                'course' => $schedule->course_name,
                'room' => $schedule->room,
                'starts_at' => $schedule->starts_at,
                'ends_at' => $schedule->ends_at,
                'time' => sprintf('%s - %s', $this->formatTime($schedule->starts_at), $this->formatTime($schedule->ends_at)),
            ];
        });

        $dayOptions = collect($orderedDays)->map(function (string $day) use ($rawSchedules) {
            $items = $rawSchedules->get($day, collect());

            return [
                'key' => $day,
                'label' => $this->translateDay($day),
                'count' => $items->count(),
            ];
        });

        return view('schedule', [
            'selectedDay' => $selectedDay,
            'selectedLabel' => $this->translateDay($selectedDay),
            'dayOptions' => $dayOptions,
            'selectedSchedules' => $selectedSchedules,
            'weeklySchedules' => $rawSchedules->map(fn (Collection $items, string $day) => [
                'label' => $this->translateDay($day),
                'items' => $items->map(fn (ClassSchedule $schedule) => [
                    'course' => $schedule->course_name,
                    'room' => $schedule->room,
                    'time' => sprintf('%s - %s', $this->formatTime($schedule->starts_at), $this->formatTime($schedule->ends_at)),
                ]),
            ]),
        ]);
    }

    protected function todayKey(): string
    {
        return strtolower(Carbon::today()->format('l'));
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
