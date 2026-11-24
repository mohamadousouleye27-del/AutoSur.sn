<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Services\ImageStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateVehiclePhotos extends Command
{
    protected $signature = 'photos:migrate';

    protected $description = 'Envoie les anciennes photos de véhicules (stockage local) vers Cloudinary';

    public function handle(ImageStorage $images): int
    {
        $vehicles = Vehicle::whereNotNull('photo_path')->whereNull('photo_public_id')->get();

        foreach ($vehicles as $vehicle) {
            $path = Storage::disk('public')->path($vehicle->photo_path);

            if (! is_file($path)) {
                $this->warn("Fichier introuvable : {$vehicle->photo_path}");
                continue;
            }

            $publicId = $images->upload($path, 'vehicles');

            Storage::disk('public')->delete($vehicle->photo_path);
            $vehicle->forceFill(['photo_public_id' => $publicId, 'photo_path' => null])->save();

            $this->info("Véhicule #{$vehicle->id} migré.");
        }

        $this->info($vehicles->count() . ' véhicule(s) traité(s).');

        return self::SUCCESS;
    }
}