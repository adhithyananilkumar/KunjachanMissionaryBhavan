<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Inmate;
use Illuminate\Http\Request;

class InmateSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $term = trim((string) $request->get('q', ''));

        if ($term === '' || mb_strlen($term) < 2) {
            return response()->json(['data' => []]);
        }

        $query = Inmate::with('institution');

        $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhereRaw("CONCAT(first_name,' ',COALESCE(last_name,'')) like ?", ["%{$term}%"])
              ->orWhere('admission_number', 'like', "%{$term}%");
        });

        $safe = str_replace(['%', '_'], ['\\%', '\\_'], $term);
        $prefix = $safe . '%';
        $contains = '%' . $safe . '%';

        $query->orderByRaw(<<<SQL
            CASE
                WHEN CONCAT(first_name,' ',COALESCE(last_name,'')) LIKE ? THEN 1
                WHEN first_name LIKE ? THEN 2
                WHEN last_name LIKE ? THEN 3
                WHEN admission_number LIKE ? THEN 4
                ELSE 10
            END
        SQL, [
            $prefix,
            $prefix,
            $prefix,
            $prefix,
        ])->orderBy('first_name')->orderBy('id');

        $results = $query->limit(15)->get()->map(function (Inmate $inmate) {
            return [
                'id' => $inmate->id,
                'name' => $inmate->full_name,
                'admission_number' => $inmate->admission_number,
                'institution' => $inmate->institution?->name,
            ];
        });

        return response()->json(['data' => $results]);
    }
}
