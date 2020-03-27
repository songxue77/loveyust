<?php

namespace App\Models;

use App\Helpers\HasSlug;
use App\Helpers\ModelHelpers;
use Illuminate\Database\Eloquent\Model;

final class Tag extends Model
{
    use HasSlug;
    use ModelHelpers;

    /**
     * {@inheritdoc}
     */
    protected $table = 'tags';

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }
}
