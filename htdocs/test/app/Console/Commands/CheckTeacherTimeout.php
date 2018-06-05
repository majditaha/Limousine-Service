<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Message;
use App\Penalty;


class CheckTeacherTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckTeacherTimeout:teacherTimeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if taken request was not answered in time and add penalty for teacher';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notFinished = Message::where('type', 'check_request')
                        ->whereNull('finished_at')
                        ->where('taken_at', '<', \Carbon\Carbon::now()->subMinutes(\App\Setting::getValue('teacherAnswerTime')))
                        ->get();
        $notFinished->each(function ($item) {
            $penalty = new Penalty;
            $penalty->teacher_id = $item->teacher_id;
            $penalty->message_id = $item->id;
            $penalty->save();

            $item->teacher_id = null;
            $item->taken_at = null;
            $item->save();
        });
    }
}
