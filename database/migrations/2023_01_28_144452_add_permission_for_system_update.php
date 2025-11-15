<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class AddPermissionForSystemUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add system update settings
        DB::table('permissions')->insert([
            'slug' => 'system_update_read',
            'name' => 'system_update',
        ]);

        DB::table('permissions')->insert([
            'slug' => 'system_update_write',
            'name' => 'system_update',
        ]);
        DB::unprepared("UPDATE `roles` SET `slug` = 'superadmin', `name` = 'Superadmin', `permissions` ='{\"users_read\":true,\"users_write\":true,\"users_delete\":true,\"settings_read\":true,\"settings_write\":true,\"settings_delete\":true,\"role_read\":true,\"role_write\":true,\"role_delete\":true,\"permission_read\":true,\"permission_write\":true,\"permission_delete\":true,\"language_settings_read\":true,\"language_settings_write\":true,\"language_settings_delete\":true,\"pages_read\":true,\"pages_write\":true,\"pages_delete\":true,\"menu_read\":true,\"menu_write\":true,\"menu_delete\":true,\"menu_item_read\":true,\"menu_item_write\":true,\"menu_item_delete\":true,\"post_read\":true,\"post_write\":true,\"post_delete\":true,\"category_read\":true,\"category_write\":true,\"category_delete\":true,\"sub_category_read\":true,\"sub_category_write\":true,\"sub_category_delete\":true,\"widget_read\":true,\"widget_write\":true,\"widget_delete\":true,\"newsletter_read\":true,\"newsletter_write\":true,\"newsletter_delete\":true,\"notification_read\":true,\"notification_write\":true,\"notification_delete\":true,\"contact_message_read\":true,\"contact_message_write\":true,\"contact_message_delete\":true,\"ads_read\":true,\"ads_write\":true,\"ads_delete\":true,\"theme_section_read\":true,\"theme_section_write\":true,\"theme_section_delete\":true,\"polls_read\":true,\"polls_write\":true,\"polls_delete\":true,\"socials_read\":true,\"socials_write\":true,\"socials_delete\":true,\"comments_read\":true,\"comments_write\":true,\"comments_delete\":true,\"album_read\":true,\"album_write\":true,\"album_delete\":true,\"rss_read\":true,\"rss_write\":true,\"rss_delete\":true,\"api_read\":true,\"api_write\":true,\"api_delete\":true,\"system_update_write\":true,\"system_update_read\":true}'
        WHERE `slug` = 'superadmin'");
        
        //version update
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $default_language   = settingHelper('default_language') ?? 'en';

        if (settingHelper('version') == ''):
            
            DB::table('settings')->insert([
                'title' => 'version',
                'value' => 143,
                'lang' => $default_language,
            ]);
        else:
            DB::table('settings')->where('title', 'version')->update([
                'value' => 143
            ]);
        endif;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Artisan::call('all:clear');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
