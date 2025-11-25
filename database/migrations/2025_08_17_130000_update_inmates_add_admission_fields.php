<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inmates', function(Blueprint $table){
            if(Schema::hasColumn('inmates','full_name') && !Schema::hasColumn('inmates','first_name')){
                $table->renameColumn('full_name','first_name');
            }
            if(!Schema::hasColumn('inmates','last_name')) $table->string('last_name')->nullable()->after('first_name');
            if(!Schema::hasColumn('inmates','registration_number')) $table->string('registration_number')->nullable()->after('id');
            if(!Schema::hasColumn('inmates','photo_path')) $table->string('photo_path')->nullable()->after('gender');
            if(!Schema::hasColumn('inmates','guardian_relation')) $table->string('guardian_relation')->nullable()->after('guardian_id');
            if(!Schema::hasColumn('inmates','guardian_first_name')) $table->string('guardian_first_name')->nullable()->after('guardian_relation');
            if(!Schema::hasColumn('inmates','guardian_last_name')) $table->string('guardian_last_name')->nullable()->after('guardian_first_name');
            if(!Schema::hasColumn('inmates','guardian_email')) $table->string('guardian_email')->nullable()->after('guardian_last_name');
            if(!Schema::hasColumn('inmates','guardian_phone')) $table->string('guardian_phone')->nullable()->after('guardian_email');
            if(!Schema::hasColumn('inmates','guardian_address')) $table->text('guardian_address')->nullable()->after('guardian_phone');
            if(!Schema::hasColumn('inmates','aadhaar_number')) $table->string('aadhaar_number')->nullable()->after('guardian_address');
            if(!Schema::hasColumn('inmates','aadhaar_card_path')) $table->string('aadhaar_card_path')->nullable()->after('aadhaar_number');
            if(!Schema::hasColumn('inmates','ration_card_path')) $table->string('ration_card_path')->nullable()->after('aadhaar_card_path');
            if(!Schema::hasColumn('inmates','panchayath_letter_path')) $table->string('panchayath_letter_path')->nullable()->after('ration_card_path');
            if(!Schema::hasColumn('inmates','disability_card_path')) $table->string('disability_card_path')->nullable()->after('panchayath_letter_path');
            if(!Schema::hasColumn('inmates','doctor_certificate_path')) $table->string('doctor_certificate_path')->nullable()->after('disability_card_path');
            if(!Schema::hasColumn('inmates','vincent_depaul_card_path')) $table->string('vincent_depaul_card_path')->nullable()->after('doctor_certificate_path');
        });
    }
    public function down(): void
    {
        Schema::table('inmates', function(Blueprint $table){
            // Note: Not reverting rename for safety; only dropping added columns.
            $cols = [ 'last_name','registration_number','photo_path','guardian_relation','guardian_first_name','guardian_last_name','guardian_email','guardian_phone','guardian_address','aadhaar_number','aadhaar_card_path','ration_card_path','panchayath_letter_path','disability_card_path','doctor_certificate_path','vincent_depaul_card_path'];
            foreach($cols as $c){ if(Schema::hasColumn('inmates',$c)) $table->dropColumn($c); }
        });
    }
};
