<?php

namespace App\Console\Commands;

use App\Models\Inmate;
use App\Models\User;
use App\Notifications\InmateBirthday;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NotifyInmateBirthdays extends Command
{
    protected $signature = 'notify:inmate-birthdays {--date=}';
    protected $description = 'Send notifications to admins/system/staff for inmates whose birthday is today';

    public function handle(): int
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $this->info('Checking birthdays for '.$date->toDateString());

        $inmates = Inmate::whereNotNull('date_of_birth')->get()->filter(fn($i)=> $i->date_of_birth?->isBirthday($date) );
        if($inmates->isEmpty()){
            $this->info('No birthdays today.');
            return self::SUCCESS;
        }

        // Group by institution to notify relevant users
        $byInstitution = $inmates->groupBy('institution_id');
        foreach($byInstitution as $institutionId => $group){
            $recipients = User::whereIn('role',['system_admin','admin','staff'])
                ->when($institutionId, fn($q)=> $q->where('institution_id', $institutionId))
                ->get();
            foreach($group as $inmate){
                foreach($recipients as $user){
                    try { $user->notify(new InmateBirthday($inmate)); }
                    catch(\Throwable $e){ $this->warn('Failed notifying user '.$user->id.': '.$e->getMessage()); }
                }
            }
        }
        $this->info('Birthday notifications sent: '.$inmates->count().' inmates.');
        return self::SUCCESS;
    }
}
