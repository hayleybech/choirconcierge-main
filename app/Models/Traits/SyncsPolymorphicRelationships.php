<?php

namespace App\Models\Traits;

trait SyncsPolymorphicRelationships
{
    /**
     * @param string $poly_class The class name of the polymorphic model
     * @param string $poly_relationship The name of the other model's relationship to the polymorph
     * @param string $poly_name The name of the polymorph used in table columns (x_id, x_type)
     * @param string $related_id_col The name of the foreign key column connecting the polymorph to the other model
     * @param array $poly_records An associative array where the keys are the model class names of each poly type and the values are arrays of ids to sync
     */
    public function syncPolymorphicMany(
        string $poly_class,
        string $poly_relationship,
        string $poly_name,
        string $related_id_col,
        array $poly_records
    ): void {
        foreach ($poly_records as $class => $records) {
            $this->syncPolymorphic($poly_class, $poly_relationship, $poly_name, $related_id_col, $class, $records);
        }
    }

    /**
     * @param string $poly_class The class name of the polymorphic model
     * @param string $poly_relationship The name of the other model's relationship to the polymorph
     * @param string $poly_name The name of the polymorph used in table columns (x_id, x_type)
     * @param string $related_id_col The name of the foreign key column connecting the polymorph to the other model
     * @param string $poly_type The model class name of type we're currently syncing
     * @param array $poly_ids The ids to sync
     */
    public function syncPolymorphic(
        string $poly_class,
        string $poly_relationship,
        string $poly_name,
        string $related_id_col,
        string $poly_type,
        array $poly_ids
    ): void {
        // Detach the records not listed in the incoming array
        $poly_class
            ::where($related_id_col, '=', $this->id)
            ->where($poly_name.'_type', '=', $poly_type)
            ->whereNotIn($poly_name.'_id', $poly_ids)
            ->delete();

        // Insert new records
        $unchanged_ids = $poly_class
            ::where($related_id_col, '=', $this->id)
            ->where($poly_name.'_type', '=', $poly_type)
            ->whereIn($poly_name.'_id', $poly_ids)
            ->pluck($poly_name.'_id')
            ->toArray();
        $new_poly_ids = array_diff($poly_ids, $unchanged_ids);

        $attach = [];
        foreach ($new_poly_ids as $new_poly_id) {
            $attach[] = [
                $poly_name.'_id' => $new_poly_id,
                $poly_name.'_type' => $poly_type,
            ];
        }

        $this->fresh()
            ->$poly_relationship()
            ->createMany($attach);
    }
}
