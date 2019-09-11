<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Entities\Member;

use Carbon\Carbon;
/**
 * Class MemberTransformer.
 *
 * @package namespace App\Transformers;
 */
class MemberTransformer extends TransformerAbstract
{
    /**
     * Transform the Member entity.
     *
     * @param \App\Entities\Member $model
     *
     * @return array
     */
    public function transform(Member $model)
    {
        return collect([
            'id' => (int)$model->id,
            'name' => $model->name,
            'email' => $model->email,
            'status' => (int)$model->status,
            'created_at' => ($model->created_at) ? Carbon::parse($model->created_at)->format('Y-m-d H:i:s') : "",
            'updated_at' => ($model->updated_at) ? Carbon::parse($model->updated_at)->format('Y-m-d H:i:s') : "",
        ])->map(function ($item, $key) {
            return $key = (is_null($item)) ? "" : $item;
        })->toArray();
    }
}
