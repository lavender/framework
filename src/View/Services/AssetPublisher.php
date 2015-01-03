<?php
namespace Lavender\View\Services;

use Illuminate\Foundation\AssetPublisher as CorePublisher;

class AssetPublisher extends CorePublisher
{
    /**
     * Copy all assets from a given path to the publish path.
     *
     * @param  string $name
     * @param  string $source
     * @return bool
     *
     * @throws \RuntimeException
     */
    public function publish($name, $source)
    {
        $success = $this->files->copyDirectory($source, $this->publishPath);

        if(!$success) throw new \RuntimeException("Unable to publish assets.");

        return $success;
    }
}