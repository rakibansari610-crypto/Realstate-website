<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('page_index')->nullable();
            $table->string('count')->nullable();
            $table->string('category')->nullable();
            $table->string('code')->nullable();
            $table->string('status')->nullable();
            $table->string('ref_no')->nullable();
            $table->string('community')->nullable();
            $table->string('property_name')->nullable();
            $table->decimal('builtup_area', 10, 2)->nullable();
            $table->decimal('plot_area', 10, 2)->nullable();
            $table->integer('bedrooms')->nullable();
            $table->string('primary_unit_view')->nullable();
            $table->string('primary_unit_view_id')->nullable();
            $table->string('secondary_unit_view')->nullable();
            $table->string('secondary_unit_view_id')->nullable();
            $table->string('unit_model')->nullable();
            $table->integer('floor_no')->nullable();
            $table->date('handover_date')->nullable();
            $table->date('contract_a_expiry')->nullable();
            $table->string('agent')->nullable();
            $table->string('contact_number')->nullable();
            $table->text('remarks')->nullable();
            $table->string('country_name')->nullable();
            $table->string('state_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('district_name')->nullable();
            $table->string('marketing_usp')->nullable();
            $table->string('financing_company')->nullable();
            $table->integer('parking')->nullable();
            $table->string('sub_type')->nullable();
            $table->decimal('sell_price', 15, 4)->nullable();
            $table->string('sub_community')->nullable();
            $table->text('property_overview')->nullable();
            $table->text('local_area_amenities_desc')->nullable();
            $table->string('document_web')->nullable();
            $table->integer('no_of_bathrooms')->nullable();
            $table->integer('ret_unit_category')->nullable();
            $table->string('pdf_brochure_link')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->text('property_ownership_desc')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('community_id')->nullable();
            $table->unsignedBigInteger('sub_community_id')->nullable();
            $table->string('agent_rera_no')->nullable();
            $table->string('pro_google_coordinates')->nullable();
            $table->string('bdm_pkg')->nullable();
            $table->string('salesman_email')->nullable();
            $table->dateTime('last_updated')->nullable();
            $table->dateTime('listing_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('recommended_properties')->nullable();
            $table->string('marketing_title')->nullable();
            $table->string('marketing_options')->nullable();
            $table->string('agent_photo')->nullable();
            $table->string('arabic_title')->nullable();
            $table->text('arabic_description')->nullable();
            $table->string('mandate')->nullable();
            $table->string('currency_abr')->nullable();
            $table->string('area_measurement')->nullable();
            $table->string('rera_str_no')->nullable();
            $table->string('furnish_status')->nullable();
            $table->string('listing_type')->nullable();
            $table->string('permit_number')->nullable();
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            // Add foreign key constraints if needed
            // $table->foreign('agent_id')->references('id')->on('developers')->onDelete('set null');
            // $table->foreign('property_id')->references('id')->on('projects')->onDelete('set null');
            // Add more FKs as per schema
        });
    }

    public function down()
    {
        Schema::dropIfExists('units');
    }
}
