<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use App\Models\Proposal;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        View::composer('*', function ($view) {

            $proposal = Proposal::with([
                'checklist.subProses'
            ])->get();

            $reminders = collect();

            foreach ($proposal as $item) {

                // hanya proposal yang progress belum selesai
                if (($item->progress ?? 0) >= 100) {
                    continue;
                }

                // checklist pertama yang belum dicentang
                $nextChecklist = $item->checklist
                    ->sortBy('sub_proses_id')
                    ->firstWhere('is_checked', 0);

                if (!$nextChecklist) {
                    continue;
                }
                
                $deadline = Carbon::parse($item->overdue);
                $sisaHari = Carbon::today()->diffInDays($deadline, false);

                $reminders->push([
                    'proposal_id' => $item->id,
                    'judul'       => $item->judul,
                    'berkas'      => $nextChecklist->subProses->nama_sub,
                    'deadline'    => $item->overdue,
                    'sisaHari'    => $sisaHari,
                ]);
            }

            $reminders = $reminders->sortBy(function ($item) {

                // Tentukan prioritas
                if ($item['sisaHari'] == 0) {
                    $priority = 1; // Hari ini
                } elseif ($item['sisaHari'] == 1) {
                    $priority = 2; // H-1
                } elseif ($item['sisaHari'] == 2) {
                    $priority = 3; // H-2
                } elseif ($item['sisaHari'] > 2) {
                    $priority = 4; // DL jauh
                } else {
                    $priority = 5; // Terlambat
                }

                return [$priority, $item['deadline']];
            })->values();

            $reminderGroups = [
                'today'    => $reminders->filter(fn ($r) => $r['sisaHari'] == 0)->values(),
                'h1'       => $reminders->filter(fn ($r) => $r['sisaHari'] == 1)->values(),
                'h2'       => $reminders->filter(fn ($r) => $r['sisaHari'] == 2)->values(),
                'overdue'  => $reminders->filter(fn ($r) => $r['sisaHari'] < 0)->values(),
                'other'    => $reminders->filter(fn ($r) => $r['sisaHari'] > 2)->values(),
            ];

            $view->with([
                'reminders' => $reminders,
                'reminderGroups' => $reminderGroups,
            ]);
        });
    }
}
