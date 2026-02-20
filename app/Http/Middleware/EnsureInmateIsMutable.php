<?php

namespace App\Http\Middleware;

use App\Models\Inmate;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EnsureInmateIsMutable
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        if (!$route) {
            return $next($request);
        }

        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        $routeName = (string) ($route->getName() ?? '');
        if ($routeName !== '' && str_contains($routeName, '.inmates.status.')) {
            return $next($request);
        }

        $inmates = [];

        // 1) Direct/implicit route model binding
        $direct = $route->parameter('inmate');
        if ($direct instanceof Inmate) {
            $inmates[] = $direct;
        }

        // 2) Indirect route bindings (appointment, labTest, medicalRecord, etc.)
        foreach ((array) $route->parameters() as $param) {
            if ($param instanceof Inmate) {
                $inmates[] = $param;
                continue;
            }

            if (!$param instanceof Model) {
                continue;
            }

            try {
                if (!empty($param->inmate_id)) {
                    $found = Inmate::find($param->inmate_id);
                    if ($found) {
                        $inmates[] = $found;
                        continue;
                    }
                }

                if (method_exists($param, 'inmate')) {
                    $maybe = $param->inmate;
                    if ($maybe instanceof Inmate) {
                        $inmates[] = $maybe;
                        continue;
                    }
                }
            } catch (\Throwable $e) {
                // best-effort; fall through
            }
        }

        // 3) Request body references (e.g. appointment create/update, guardian linking, doctor assignment lists)
        $inmateId = $request->input('inmate_id');
        if (is_scalar($inmateId) && $inmateId !== '') {
            $found = Inmate::find((int) $inmateId);
            if ($found) {
                $inmates[] = $found;
            }
        }

        $inmateIds = $request->input('inmate_ids');
        if (is_array($inmateIds) && !empty($inmateIds)) {
            $ids = array_values(array_unique(array_map('intval', array_filter($inmateIds, fn($v) => is_scalar($v) && $v !== ''))));
            if (!empty($ids)) {
                $found = Inmate::whereIn('id', $ids)->get();
                foreach ($found as $i) {
                    $inmates[] = $i;
                }
            }
        }

        // No inmate context found; allow.
        if (empty($inmates)) {
            return $next($request);
        }

        // De-duplicate and enforce
        $byId = [];
        foreach ($inmates as $i) {
            if ($i instanceof Inmate) {
                $byId[$i->id] = $i;
            }
        }
        $inmates = array_values($byId);

        $blocked = null;
        foreach ($inmates as $i) {
            $status = $i->status ?: Inmate::STATUS_PRESENT;
            if ($status !== Inmate::STATUS_PRESENT) {
                $blocked = $i;
                break;
            }
        }

        if (!$blocked) {
            return $next($request);
        }

        $status = $blocked->status ?: Inmate::STATUS_PRESENT;
        $message = count($inmates) > 1
            ? 'One or more selected inmates are not active. Changes are not allowed.'
            : match ($status) {
                Inmate::STATUS_DECEASED => 'This inmate is marked as deceased. Changes are not allowed.',
                Inmate::STATUS_DISCHARGED => 'This inmate is discharged. Changes are not allowed until re-joined.',
                Inmate::STATUS_TRANSFERRED => 'This inmate is transferred. Changes are not allowed until re-joined.',
                default => 'This inmate is not active. Changes are not allowed.',
            };

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => false,
                'message' => $message,
            ], 403);
        }

        return back()->with('error', $message);
    }
}
