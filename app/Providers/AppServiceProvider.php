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

                $reminders->push([
                    'proposal_id' => $item->id,
                    'judul'       => $item->judul,
                    'berkas'      => $nextChecklist->subProses->nama_sub,
                    'deadline'    => $nextChecklist->deadline,
                ]);
            }

            $view->with('reminders', $reminders);

        });
    }
}
