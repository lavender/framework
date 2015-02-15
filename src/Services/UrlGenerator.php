<?php
namespace Lavender\Services;

use Illuminate\Routing\UrlGenerator as CoreUrlGenerator;

class UrlGenerator extends CoreUrlGenerator
{

    /**
     * Generate a URL to an application asset.
     *
     * @param  string $path
     * @param  bool|null $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        $path = $this->fallback($path);

        return parent::asset($path, $secure);
    }

    /**
     * Find the asset based on theme fallbacks
     *  ex: theme "foo" inherits "bar" which inherits "default"
     *  first look in:          assets/foo/$path
     *  if not found look for:  assets/bar/$path
     *  finally check:          assets/default/$path
     *  else:                   $asset
     *
     * @param $asset
     * @return string
     */
    public function fallback($asset)
    {
        $asset_path = $asset;

        foreach(app('theme')->fallbacks as $fallback){

            $asset_path = 'assets/'.$fallback.'/'.$asset;

            $filepath = public_path($asset_path);

            if(file_exists($filepath)) return $asset_path;

        }

        return $asset_path;
    }
}
