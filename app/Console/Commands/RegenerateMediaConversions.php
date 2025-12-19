<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob;
use Spatie\MediaLibrary\Conversions\ConversionCollection;
use Spatie\MediaLibrary\Conversions\FileManipulator;

class RegenerateMediaConversions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:regenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate media conversions for all existing media files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Regenerating media conversions...');

        // Get all media files
        $mediaFiles = Media::all();
        $total = $mediaFiles->count();
        $this->info("Found {$total} media files to process.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $success = 0;
        $failed = 0;

        $fileManipulator = app(FileManipulator::class);
        
        foreach ($mediaFiles as $media) {
            try {
                // Get the model instance
                $model = $media->model;
                if ($model) {
                    // Get conversions for this media
                    $conversions = ConversionCollection::createForMedia($media);
                    // Perform conversions synchronously
                    $fileManipulator->performConversions($conversions, $media);
                }
                $success++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed to regenerate conversions for media ID {$media->id}: " . $e->getMessage());
                $failed++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Conversion regeneration completed!");
        $this->info("Success: {$success}");
        $this->info("Failed: {$failed}");

        return Command::SUCCESS;
    }
}
