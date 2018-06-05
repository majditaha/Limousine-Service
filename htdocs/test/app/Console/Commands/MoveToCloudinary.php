<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MoveToCloudinary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move-to-cloudinary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $cloudName = config('services.cloudinary.cloud_name');
        $uploadPreset = config('services.cloudinary.upload_preset');

        $uploadcareStart = 'https://ucarecdn.com';

        \App\User::get()->each(function ($item) use ($uploadcareStart, $uploadPreset) {
            $filename = $item->passport_file;
            if ($filename && starts_with($filename, $uploadcareStart)) {
                $result = \Cloudder::upload($filename, null, ['upload_preset' => $uploadPreset])->getResult();
                $item->passport_file = $result['secure_url'];
                $item->save();
            }

            $filename = $item->empl_history_file;
            if ($filename && starts_with($filename, $uploadcareStart)) {
                $result = \Cloudder::upload($filename, null, ['upload_preset' => $uploadPreset])->getResult();
                $item->empl_history_file = $result['secure_url'];
                $item->save();
            }
        });

        \App\Discipline::get()->each(function ($item) use ($uploadcareStart, $uploadPreset) {
            $filename = $item->icon_file;
            if ($filename && starts_with($filename, $uploadcareStart)) {
                $result = \Cloudder::upload($filename, null, ['upload_preset' => $uploadPreset])->getResult();
                $item->icon_file = $result['secure_url'];
                $item->save();
            }
        });

        \App\File::get()->each(function ($item) use ($uploadcareStart, $uploadPreset) {
            $filename = $item->url;
            if ($filename && starts_with($filename, $uploadcareStart)) {
                $result = \Cloudder::upload($filename, null, ['upload_preset' => $uploadPreset])->getResult();
                $item->url = $result['secure_url'];
                $item->save();
            }
        });

        \App\Practice::get()->each(function ($item) use ($uploadcareStart, $uploadPreset) {
            $filename = $item->text_pdf;
            if ($filename && $filename['url_pdf'] && starts_with($filename['url_pdf'], $uploadcareStart)) {
                $result = \Cloudder::upload($filename['url_pdf'], null, ['upload_preset' => $uploadPreset])->getResult();
                $result['url_pdf'] = $result['secure_url'];
                $result['cdn'] = 'cloudinary';
                $item->text_pdf = $result;
                $item->save();
            }

            $filename = $item->hint_pdf;
            if ($filename && $filename['url_pdf'] && starts_with($filename['url_pdf'], $uploadcareStart)) {
                $result = \Cloudder::upload($filename['url_pdf'], null, ['upload_preset' => $uploadPreset])->getResult();
                $result['url_pdf'] = $result['secure_url'];
                $result['cdn'] = 'cloudinary';
                $item->hint_pdf = $result;
                $item->save();
            }

            $filename = $item->solution_pdf;
            if ($filename && $filename['url_pdf'] && starts_with($filename['url_pdf'], $uploadcareStart)) {
                $result = \Cloudder::upload($filename['url_pdf'], null, ['upload_preset' => $uploadPreset])->getResult();
                $result['url_pdf'] = $result['secure_url'];
                $result['cdn'] = 'cloudinary';
                $item->solution_pdf = $result;
                $item->save();
            }
        });

        \App\Theory::get()->each(function ($item) use ($uploadcareStart, $uploadPreset) {
            $filename = $item->text_pdf;
            if ($filename && $filename['url_pdf'] && starts_with($filename['url_pdf'], $uploadcareStart)) {
                $result = \Cloudder::upload($filename['url_pdf'], null, ['upload_preset' => $uploadPreset])->getResult();
                $result['url_pdf'] = $result['secure_url'];
                $result['cdn'] = 'cloudinary';
                $item->text_pdf = $result;
                $item->save();
            }
        });
    }
}
