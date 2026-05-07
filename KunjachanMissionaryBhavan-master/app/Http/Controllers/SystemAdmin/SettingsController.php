<?php
namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function medicationWindows()
    {
        $windows = config('medication.windows');
        return view('system_admin.settings.medication_windows', compact('windows'));
    }

    public function saveMedicationWindows(Request $request)
    {
        $data = $request->validate([
            'morning_start' => 'required|date_format:H:i',
            'morning_end'   => 'required|date_format:H:i|after:morning_start',
            'noon_start'    => 'required|date_format:H:i',
            'noon_end'      => 'required|date_format:H:i|after:noon_start',
            'night_start'   => 'required|date_format:H:i',
            'night_end'     => 'required|date_format:H:i|after:night_start',
        ]);

        $envUpdates = [
            'MED_WINDOW_MORNING_START' => $data['morning_start'],
            'MED_WINDOW_MORNING_END'   => $data['morning_end'],
            'MED_WINDOW_NOON_START'    => $data['noon_start'],
            'MED_WINDOW_NOON_END'      => $data['noon_end'],
            'MED_WINDOW_NIGHT_START'   => $data['night_start'],
            'MED_WINDOW_NIGHT_END'     => $data['night_end'],
        ];

        $this->writeEnv($envUpdates);

        return back()->with('success','Medication windows updated.');
    }

    private function writeEnv(array $data): void
    {
        $path = base_path('.env');
        if(!is_writable($path)){
            abort(500, '.env not writable');
        }
        $env = file_get_contents($path);
        foreach($data as $key=>$value){
            $pattern = "/^{$key}=.*$/m";
            if(preg_match($pattern, $env)){
                $env = preg_replace($pattern, $key.'='.$value, $env);
            } else {
                $env .= PHP_EOL.$key.'='.$value;
            }
        }
        file_put_contents($path, $env);
    }
}
