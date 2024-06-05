<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCampaignElementIdFromCampaignElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_elements', function (Blueprint $table) {
            $table->dropColumn('campaign_element_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_elements', function (Blueprint $table) {
            $table->string('campaign_element_id');
        });
    }
}
