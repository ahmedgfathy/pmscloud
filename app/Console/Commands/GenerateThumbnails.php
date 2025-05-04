<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;

class GenerateThumbnails extends Command
{
    protected $signature = 'files:thumbnails';
    protected $description = 'Generate thumbnails for image files';

    public function handle()
    {
        $files = File::where('mime_type', 'like', 'image/%')->get();
        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            try {
                if (!str_starts_with($file->mime_type, 'image/')) {
                    continue;
                }
                $file->getPreviewAttribute();
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("Error processing {$file->name}: {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('Thumbnails generated successfully');
    }
}
