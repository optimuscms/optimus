<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Optix\Media\Models\Media;

/**
 * @property Meta $meta
 */
trait HasSeoFields
{
    /**
     * Define the "seoFields" relationship.
     *
     * @return MorphOne
     */
    public function meta()
    {
        /* @var Model $this */
        return $this->morphOne(Meta::class, 'metable');
    }

    public function saveMeta(array $data = [])
    {
        if (! empty($data)) {
            $this->load('meta');
            $this->meta ? $this->meta()->update($data) : $this->meta()->create($data);
            $this->load('meta');

            // Attach OG image
            if (! empty($data['og_image_id'])) {
                $media = Media::findOrFail($data['og_image_id']);
                $this->meta->attachMedia($media, Meta::OG_MEDIA_GROUP, [Meta::OG_MEDIA_CONVERSION]);
            }
        }
    }
}
